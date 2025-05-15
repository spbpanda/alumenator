<?php

namespace App\Http\Controllers\Admin;

use App\Events\PaymentPaid;
use App\Http\Controllers\ItemsController;
use App\Http\Requests\CreateManualPaymentRequest;
use App\Jobs\FinalHandlerJob;
use App\Jobs\ManualPaymentCommandProccessingJob;
use App\Models\Ban;
use App\Models\CartItem;
use App\Models\Cart;
use App\Models\Category;
use App\Models\CmdQueue;
use App\Models\DiscordRoleQueue;
use App\Models\Gift;
use App\Models\Item;
use App\Models\CommandHistory;
use App\Models\Currency;
use App\Models\DonationGoal;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\Setting;
use App\Models\SecurityLog;
use Carbon\Carbon;
use Crypt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

class PaymentsController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Transactions'));
        $this->loadSettings();
    }

    public function index(): View|RedirectResponse
    {
        if (!UsersController::hasRule('payments', 'read')) {
            return redirect('/admin');
        }

        $payments = Payment::query()->with(['user'])->orderBy('created_at', 'DESC')->paginate(10);

        $isDetailsEnabled = Setting::query()->find(1)->details;

        return view('admin.payments.index', compact('payments', 'isDetailsEnabled'));
    }

    public function create(): View|RedirectResponse
    {
        if (!UsersController::hasRule('payments', 'write')) {
            return redirect('/admin');
        }

        $packages = Item::query()
            ->where('deleted', 0)
            ->select(['id', 'name', 'price', 'category_id'])
            ->with(['category' => function($query) {
                $query->select(['id', 'name']);
            }])
            ->get();

        $gateways = PaymentMethod::orderBy('name')
            ->get();

        return view('admin.payments.create', compact('packages', 'gateways'));
    }

    public function store(CreateManualPaymentRequest $request): RedirectResponse
    {
        if (!UsersController::hasRule('payments', 'write')) {
            return redirect('/admin');
        }

        $email = $request->email ?? "";

        $settings = Setting::find(1);
        if (!empty($email) && $request->send_mail == 1) {
            if (!$settings->details) {
                return redirect()->route('payments.create')->with('warning', __('Enable "Collecting Customer Details" at the "Transactions" tab to send emails.'));
            }

            if (!$settings->smtp_enable) {
                return redirect()->route('payments.create')->with('warning', __('Enable SMTP at the "Settings" tab to send emails.'));
            }
        }

        $user = User::query()->where([
            ['identificator', $request->username],
            ['system', 'minecraft']
        ])->first();
        if (!$user) {
            $user = User::create([
                'username' => $request->username,
                'avatar' => 'https://mc-heads.net/body/' . $request->username,
                'system' => 'minecraft',
                'identificator' => $request->username,
                'api_token' => Str::random(60),
            ]);
        }

        $userId = $user->id;
        if (empty($userId)) {
            return to_route('payments.create');
        }

        $items = $request->packages;

        try {
            DB::beginTransaction();
            $cart = Cart::query()->create([
                'user_id' => $userId,
                'items' => count($items),
                'price' => $request->price,
                'is_active' => 0,
                'referral' => null,
            ]);

            $cartItems = [];
            foreach ($items as $item) {
                $item = Item::query()->select('id', 'price')->find($item);
                $cartItems[] = [
                    'cart_id' => $cart->id,
                    'item_id' => $item->id,
                    'price' => $item->price,
                    'payment_type' => 0,
                    'count' => 1,
                ];
            }
            CartItem::insert($cartItems);

            $paymentData = [
                'user_id' => $userId,
                'cart_id' => $cart->id,
                'price' => str_replace(',', '.', $request->price),
                'status' => Payment::PAID,
                'currency' => $this->settings->currency ?? 'USD',
                'ref' => null,
                'details' => '',
                'ip' => '0.0.0.0',
                'gateway' => $request->gateway ?? 'manual',
                'transaction' => $request->transaction ?? '',
                'note' => $request->note ?? '',
            ];

            if ($this->settings->details === 1) {
                $paymentData['details'] = json_encode([
                    'fullname' => '',
                    'email' => $request->email,
                    'address1' => '',
                    'address2' => '',
                    'city' => '',
                    'region' => '',
                    'country' => '',
                    'zipcode' => '',
                ]);
            }

            // Check if any donation goal is active and increment it
            $donationGoal = DonationGoal::where('status', 1)->first();
            if ($donationGoal) {
                DonationGoalsController::increment(str_replace(',', '.', $request->price));
            }

            $payment = Payment::create($paymentData);

            event(new PaymentPaid($payment));

            // Execute commands attached to these packages
            if ($request->payment_is_execute == 'on') {
                ManualPaymentCommandProccessingJob::dispatch($payment);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return to_route('payments.index');
        }

        if ($request->send_mail == 1 && $settings->smtp_enable && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            //$user->notify(new SuccessfulPayment($payment));

            // Configure PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Decrypt SMTP password
                $smtp_password = Crypt::decryptString($settings->smtp_pass);

                $mail->isSMTP();
                $mail->Host = $settings->smtp_host;
                $mail->SMTPAuth = true;
                $mail->Username = $settings->smtp_user;
                $mail->Password = $smtp_password;
                //$mail->SMTPSecure = 'tls';
                $mail->Port = $settings->smtp_port;
                $mail->CharSet = 'utf-8';

                $mail->setFrom($settings->smtp_user, $settings->site_name);
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Thank you for your purchase!';
                $site_name = $settings->site_name;
                $total = $payment->price;
                $username = $user->username;

                $items = [];
                $cartItems = CartItem::where('cart_id', $payment->cart->id)->get();
                foreach ($cartItems as $cartItem) {
                    $items[] = [
                        'id' => $cartItem->item->id,
                        'name' => $cartItem->item->name,
                        'qty' => $cartItem->count,
                        'price' => $cartItem->count * $cartItem->item->price,
                        'currency' => $payment->currency, // Use payment currency
                    ];
                }

                $mail->Body = view('emails.order', compact('site_name', 'username', 'payment', 'items', 'total'))->render();

                $mail->send();
            } catch (PHPMailerException $e) {
                Log::error('Mail Error PHPMailer: ' . $e->getMessage()); // Log specific error message
            }
        }

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::CREATE_METHOD,
            'action' => SecurityLog::ACTION['payments'],
            'action_id' => $payment->id,
        ]);

        return to_route('payments.show', $payment->id);
    }

    public function show(int $id): View|RedirectResponse
    {
        if (!UsersController::hasRule('payments', 'read')) {
            return redirect('/admin');
        }

        $payment = Payment::query()->with(['user', 'cart', 'chargeback', 'сommand_history'])->findOrFail($id);
        $items = CartItem::query()->with(['item'])->where('cart_id', $payment->cart_id)->get();
        $discordRoles = DiscordRoleQueue::query()->with(['role'])->where('payment_id', $payment->id)->get();

        // Updated in 3.0: Added own UUID API
        $get_uuid = 'https://minestorecms.com/api/uuid/name/' . $payment->user->username;

        $handle = curl_init();

        curl_setopt($handle, CURLOPT_URL, $get_uuid);
        curl_setopt($handle, CURLOPT_POST, false);
        curl_setopt($handle, CURLOPT_BINARYTRANSFER, false);
        curl_setopt($handle, CURLOPT_HEADER, true);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);

        $response = curl_exec($handle);
        $hlength = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $uuid_json = substr($response, $hlength);

        // If HTTP response is not 200, throw exception
        if ($httpCode != 200) {
            $uuid = "User doesn't exists!";
        }

        if ($uuid_json) {
            $uuid = json_decode($uuid_json, true);
            if ($uuid && isset($uuid['uuid'])) {
                $uuid = $uuid['uuid'];
            } else {
                $uuid = __("Can't get the UUID for this user.");
            }
        } else {
            $uuid = __("Can't get the UUID for this user.");
        }

        $system_price = [];
        $system_currency = Currency::query()->where('name', $this->settings->currency)->first();

        if ($payment->currency != $system_currency->name) {
            $currencyRate = Currency::query()->where('name', $payment->currency)->first();
            if ($currencyRate) {
                $sysPrice = round($this->toActualCurrency($payment->price, $system_currency->value, $currencyRate->value), 2);
                $system_price = [$sysPrice, $system_currency->name];
            }
        }

        $isVirtualCurrency = false;
        if (Setting::query()->find(1)->virtual_currency && $payment->cart->virtual_price > 0) {
            $isVirtualCurrency = true;
        }

        $cart = Cart::query()->where('id', $payment->cart_id)->first();
        if ($cart->virtual_price > 0) {
            $virtual_currency = Setting::query()->find(1)->virtual_currency;
            $system_price = [$cart->virtual_price, $virtual_currency];
        }

        $details = null;
        if (!empty($payment->details)) {
            $details = json_decode($payment->details, true);
        }

        $ban = Ban::where('username', $payment->user->username)->first();

        $giftcards = Gift::where('payment_id', $payment->id)->get();

        return view('admin.payments.show', compact('payment', 'items', 'discordRoles', 'uuid', 'system_price', 'details', 'ban', 'system_currency', 'isVirtualCurrency', 'giftcards'));
    }

    public function resend(int $id): JsonResponse {
        if (!UsersController::hasRule('payments', 'write')) {
            return response()->json([
                'status' => false
            ], 403);
        }

        $cmd = CommandHistory::where('id', $id)->first();
        if (empty($cmd))
            return response()->json([
                'status' => false
            ], 404);

        $resendCMD = $cmd->replicate();
        $resendCMD->status = CommandHistory::STATUS_QUEUE;
        $resendCMD->save();

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['payments'],
            'action_id' => $id,
            'extra' => 'Resent command (' . $resendCMD->cmd . ') for payment #' . $cmd->payment_id,
        ]);

        return response()->json([
            'status' => true
        ]);
    }

    /**
     * Mark a payment as paid.
     *
     * @param int $id The ID of the payment to mark as paid.
     * @return JsonResponse A JSON response indicating the success or failure of the operation.
     */
    public function markAsPaid(int $id): JsonResponse {
        if (!UsersController::hasRule('payments', 'write')) {
            return response()->json([
                'status' => false
            ], 403);
        }

        $payment = Payment::where('id', $id)->first();
        if (empty($payment))
            return response()->json([
                'status' => false
            ], 404);

        if ($payment->status == Payment::COMPLETED || $payment->status == Payment::PAID)
            return response()->json([
                'status' => false
            ], 400);

        $cmd = CommandHistory::where('id', $id)->first();
        if (empty($cmd)) {
            FinalHandlerJob::dispatch($payment->id);
        }

        sleep(3);

        $payment->update([
            'status' => Payment::PAID
        ]);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['payments'],
            'action_id' => $id,
            'extra' => 'Marked payment as paid. Payment #' . $payment->id,
        ]);

        return response()->json([
            'status' => true
        ]);
    }

    public function resendAllCommands(int $id): JsonResponse {
        if (!UsersController::hasRule('payments', 'write')) {
            return response()->json([
                'status' => false
            ], 403);
        }

        $payment = Payment::where('id', $id)->first();
        if (empty($payment))
            return response()->json([
                'status' => false,
                'message' => 'Payment not found!'
            ], 404);

        $commands = CommandHistory::where('payment_id', $id)
            ->get();

        if (!empty($commands)) {
            foreach ($commands as $cmd) {
                $resendCMD = $cmd->replicate();
                $resendCMD->status = CommandHistory::STATUS_QUEUE;
                $resendCMD->save();
                $cmdQueue = CmdQueue::where('commands_history_id', $cmd->id)
                    ->first();
                if (!empty($cmdQueue)) {
                    $cmdQueue->delete();
                }
                $cmd->delete();
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No commands found!'
            ], 404);
        }

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['payments'],
            'action_id' => $id,
            'extra' => 'Resent all commands for payment #' . $cmd->payment_id,
        ]);

        return response()->json([
            'status' => true
        ]);
    }

    public function deleteCMD(int $id): JsonResponse {
        if (!UsersController::hasRule('payments', 'write')) {
            return response()->json([
                'status' => false
            ], 403);
        }

        $cmd = CommandHistory::where('id', $id)->first();
        if (empty($cmd))
            return response()->json([
                'status' => false,
                'message' => 'Command not found!'
            ], 404);

        if ($cmd->status == CommandHistory::STATUS_EXECUTED)
            return response()->json([
                'status' => false,
                'message' => 'Command already executed!'
            ], 400);

        if ($cmd->status == CommandHistory::STATUS_DELETED)
            return response()->json([
                'status' => false,
                'message' => 'Command already deleted!'
            ], 400);

        $cmdQueue = CmdQueue::where('commands_history_id', $cmd->id)
            ->first();

        if (!empty($cmdQueue)) {
            $cmdQueue->delete();
        }

        $cmd->status = CommandHistory::STATUS_DELETED;
        $cmd->save();

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::DELETE_METHOD,
            'action' => SecurityLog::ACTION['payments'],
            'action_id' => $id,
            'extra' => 'Deleted command (' . $cmd->cmd . ') from payment #' . $cmd->payment_id,
        ]);

        return response()->json([
            'status' => true
        ]);
    }

    /**
     * Delete from database
     * @param int $id
     * @return RedirectResponse
     * @return RedirectResponse|JsonResponse
     */
    public function destroy(int $id): RedirectResponse|JsonResponse
    {
        if (!UsersController::hasRule('payments', 'del')) {
            return redirect('/admin');
        }

        try {
            $payment = Payment::query()->select('cart_id')->find($id);
            $paymentRecord = Payment::query()->where('id', $id)->first();
            Cart::query()->where('id', $payment->cart_id)->delete();
            $paymentRecord->delete();

            SecurityLog::create([
                'admin_id' => \Auth::guard('admins')->user()->id,
                'method' => SecurityLog::DELETE_METHOD,
                'action' => SecurityLog::ACTION['payments'],
                'action_id' => $id,
            ]);
        } catch (\Exception $e) {
        }

        if (request()->has('ajax'))
            return response()->json(['status' => 'true']);

        return to_route('admin.payments.index');
    }
}
