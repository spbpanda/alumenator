<?php

namespace App\Jobs\Tebex;

use App\Integrations\Tebex\Migration;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Item;
use App\Models\Payment;
use App\Models\PlatformMigrationLog;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MigratePaymentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $identifier;
    protected int $migration_id;
    protected int $startPage;
    protected ?int $endPage;

    /**
     * Create a new job instance.
     */
    public function __construct($identifier, $migration_id, $startPage = 1, $endPage = null)
    {
        $this->identifier = $identifier;
        $this->migration_id = $migration_id;
        $this->startPage = $startPage;
        $this->endPage = $endPage;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting MigratePaymentsJob', [
            'identifier' => $this->identifier,
            'migration_id' => $this->migration_id,
            'start_page' => $this->startPage,
            'end_page' => $this->endPage
        ]);

        $tebex = app(Migration::class);
        $currentPage = $this->startPage;
        $lastPage = null;

        do {
            Log::info('Processing payments page', ['page' => $currentPage]);
            $paymentsData = $tebex->getPaymentsPaginated($this->identifier, $currentPage);

            if (empty($paymentsData['data'])) {
                Log::warning('No payments found on page', ['page' => $currentPage]);
                break;
            }
            if ($lastPage === null && isset($paymentsData['last_page'])) {
                $lastPage = $paymentsData['last_page'];
                Log::info('Total payment pages found', ['last_page' => $lastPage]);
            }
            foreach ($paymentsData['data'] as $payment) {
                $this->processPayment($payment);
            }

            if ($this->endPage !== null && $currentPage >= $this->endPage) {
                Log::info('Reached specified end page', ['page' => $currentPage]);
                break;
            }

            $currentPage++;
            if ($lastPage !== null && $currentPage > $lastPage) {
                Log::info('Processed all payment pages', ['total_pages' => $lastPage]);
                break;
            }

        } while (true);

        Log::info('Completed MigratePaymentsJob', [
            'identifier' => $this->identifier,
            'migration_id' => $this->migration_id,
            'pages_processed' => ($currentPage - $this->startPage)
        ]);
    }

    /**
     * Process and store a single payment
     */
    private function processPayment($data): void
    {
        if ($data['status'] !== 'Complete') {
            Log::debug('Skipping non-complete payment', [
                'payment_id' => $data['id'],
                'status' => $data['status']
            ]);
            return;
        }

        $paymentExists = PlatformMigrationLog::where('external_id', $data['id'])
            ->where('type', PlatformMigrationLog::TYPE_PAYMENT)
            ->first();

        if ($paymentExists) {
            Log::debug('Payment already migrated, skipping', [
                'external_id' => $data['id']
            ]);
            return;
        }

        $packages = [];
        if (!empty($data['packages'])) {
            foreach ($data['packages'] as $package) {
                $packageLog = PlatformMigrationLog::where('external_id', $package['id'])
                    ->where('type', PlatformMigrationLog::TYPE_PACKAGE)
                    ->first();

                if ($packageLog) {
                    $packages[] = [
                        'id' => $packageLog->internal_id
                    ];
                } else {
                    Log::warning('Package not found for payment', [
                        'payment_id' => $data['id'],
                        'package_id' => $package['id'],
                        'package_name' => $package['name'] ?? 'Unknown'
                    ]);
                }
            }
        }

        try {
            $paymentDate = Carbon::parse($data['date']);

            $paymentData = [
                'amount' => $data['amount'],
                'gateway_name' => $data['gateway']['name'] ?? 'Unknown',
                'currency_iso' => $data['currency']['iso_4217'] ?? 'USD',
                'player_name' => $data['player']['name'] ?? 'Unknown',
                'player_uuid' => $data['player']['uuid'] ?? null,
                'packages' => $packages,
            ];

            $user = User::updateOrCreate([
                'username' => $paymentData['player_name'],
            ], [
                'identificator' => $paymentData['player_name'],
                'uuid' => $paymentData['player_uuid'],
                'avatar' => 'https://mc-heads.net/body/'.$paymentData['player_name'].'/150px',
                'system' => 'minecraft',
                'api_token' => User::where('username', $paymentData['player_name'])->exists() ? null : Str::random(60),
            ]);

            $cart = Cart::create([
                'user_id' => $user->id,
                'items' => count($packages) ?? 0,
                'price' => $paymentData['amount'],
                'clear_price' => $paymentData['amount'],
                'tax' => 0,
                'is_active' => 0,
            ]);

            foreach ($packages as $package) {
                $item = Item::find($package['id']);

                if ($item) {
                    CartItem::create([
                        'cart_id' => $cart->id,
                        'item_id' => $item->id,
                        'payment_type' => CartItem::REGULAR_PAYMENT,
                        'is_promoted' => 0,
                        'price' => $item->price,
                        'initial_price' => $item->price,
                        'count' => 1,
                    ]);
                }
            }

            $payment = Payment::create([
                'internal_id' => null,
                'user_id' => $user->id,
                'cart_id' => $cart->id,
                'price' => $paymentData['amount'],
                'status' => Payment::PAID,
                'currency' => $paymentData['currency_iso'],
                'ref' => null,
                'details' => null,
                'ip' => null,
                'gateway' => $paymentData['gateway_name'],
                'transaction' => $data['id'],
                'internal_subscription_id' => null,
                'note' => 'Payment migrated from Tebex. Internal Tebex ID: ' . $data['id'],
                'discord_id' => null,
                'tax_inclusive' => 0,
                'created_at' => $paymentDate,
                'updated_at' => $paymentDate,
            ]);

            PlatformMigrationLog::create([
                'type' => PlatformMigrationLog::TYPE_PAYMENT,
                'internal_id' => $payment->id,
                'external_id' => $data['id'],
                'migration_id' => $this->migration_id,
            ]);

            Log::info('Payment migrated successfully', [
                'payment_id' => $data['id'],
                'payment_internal_id' => $payment->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process payment', [
                'external_id' => $data['id'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
