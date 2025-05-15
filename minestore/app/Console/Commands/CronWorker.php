<?php

namespace App\Console\Commands;

use App\Http\Controllers\ItemsController;
use App\Models\CartSelectServer;
use App\Models\Category;
use App\Models\CommandHistory;
use App\Models\Coupon;
use App\Models\DonationGoal;
use App\Models\Item;
use App\Models\ItemServer;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\SaleApply;
use App\Models\Server;
use App\Models\Setting;
use App\Models\Advert;
use App\Models\Gift;
use App\Models\Command as ServerCommand;
use App\Models\CommandHistory as ServerCommandHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

// use Illuminate\Support\Facades\Log;

class CronWorker extends Command
{
    protected $signature = 'cron:worker';

    protected $description = 'Command process cron actions';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        while (true) {
            $MySQL_DATATIME = Carbon::now()->format('Y-m-d H:i:00');

            Item::query()->where([
                ['deleted', '=', '0'],
                ['active', '=', '0'],
                ['publishAt', '<>', 'null'],
                ['publishAt', '<=', $MySQL_DATATIME],
            ])->update([
                'active' => '1',
                'publishAt' => 'null',
            ]);

            Item::query()->where([
                ['deleted', '=', '1'],
                ['active', '=', '1'],
                ['showUntil', '<>', 'null'],
                ['showUntil', '<=', $MySQL_DATATIME],
            ])->update([
                'active' => '0',
                'showUntil' => 'null',
            ]);

            $expireItems = DB::table('payments')
              ->join('carts', 'carts.id', '=', 'payments.cart_id')
              ->join('users', 'users.id', '=', 'carts.user_id')
              ->join('cart_items', 'cart_items.cart_id', '=', 'carts.id')
              ->join('items', 'items.id', '=', 'cart_items.item_id')
              ->select('items.id', 'items.name', 'items.price', 'users.username', 'users.ip_address', 'users.uuid', DB::raw('cart_items.id as cart_items_id'), 'cart_items.cart_id', 'cart_items.count', DB::raw('payments.id as payment_id'), 'items.is_server_choice')
              ->where([
                  ['payments.status', '=', '1'],
                  ['items.expireAfter', '>', '0'],
                  ['payments.updated_at', '<=', DB::raw('DATE_SUB(NOW(), INTERVAL CAST(`items`.`expireAfter` AS UNSIGNED) MINUTE)')],
              ])
                ->get();

            $setting = Setting::query()->find(1);

            foreach ($expireItems as $expireItem) {
                $servers = collect([]);

                if ($expireItem->is_server_choice == 1){
                    $cartSelectServers = CartSelectServer::where([['cart_id', $expireItem->cart_id], ['item_id', $expireItem->id]])->get();
                    if (!$cartSelectServers->isEmpty())
                        $servers = $cartSelectServers->servers()->where('deleted', 0)->get();
                }

                if ($servers->isEmpty())
                {
                    $servers = ItemServer::where([['type', ItemServer::TYPE_CMD_SERVER], ['item_id', $expireItem->id]])->get();
                    if ($servers->isEmpty())
                    {
                        $servers = Server::where('deleted', 0)->get();
                    } else {
                        $servers = Server::whereIn('id', $servers->pluck('server_id')->toArray())->get();
                    }
                }

                $cart = (object) ['count' => $expireItem->count, 'id' => $expireItem->cart_items_id, 'cart_id' => $expireItem->cart_id];

                if ($setting->withdraw_game === 'minecraft') {
                    $user = (object)['username' => $expireItem->username, 'ip_address' => $expireItem->ip_address, 'uuid' => $expireItem->uuid];
                    if (ItemsController::giveItem(ServerCommand::EVENT_REMOVED, $servers, $expireItem, $user, $cart, $expireItem->id)){
                        Payment::where('id', $expireItem->payment_id)->update(['status' => 3]);
                    }
                }
            }

            $refs = DB::table('payments')
              ->join('ref_cmd', 'ref_cmd.ref_id', '=', 'payments.ref')
              ->join('ref_codes', 'ref_codes.id', '=', 'payments.ref')
              ->join(DB::raw('users AS u'), 'u.id', '=', 'payments.user_id')
              ->leftJoin(DB::raw('users AS r'), 'r.username', '=', 'ref_codes.referer')
              ->select('ref_cmd.ref_id', 'ref_codes.referer', DB::raw('r.username AS ref_username'), DB::raw('r.uuid AS ref_uuid'), DB::raw('r.ip_address AS ref_ip_address'), DB::raw('u.username'), DB::raw('u.uuid'), DB::raw('u.ip_address'), 'ref_codes.commands', DB::raw('payments.id as payment_id'), 'payments.cart_id')
              ->where('ref_codes.cmd', 1)
              ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED])
              ->get();
            foreach ($refs as $ref) {
                $servers = ItemServer::where([['type', ItemServer::TYPE_REF_COMMAND_SERVER], ['item_id', $ref->ref_id]])->get();
                if ($servers->isEmpty()){
                    $servers = Server::where('deleted', 0)->get();
                } else {
                    $servers = Server::whereIn('id', $servers->pluck('server_id')->toArray())->get();
                }

                foreach ($servers as $server) {
                    if ($setting->withdraw_game == 'minecraft') {
                        $sourceCommands = json_decode($ref->commands, true);
                        for ($i = 0; $i < count($sourceCommands); $i++) {
                            $cmd = str_replace('{inviter}', $ref->ref_username, $sourceCommands[$i]['command']);
                            $cmd = str_replace('{inviter_uuid}', $ref->ref_uuid, $cmd);
                            $cmd = str_replace('{inviterIP}', $ref->ref_ip_address, $cmd);
                            $cmd = str_replace('{referral}', $ref->username, $cmd);
                            $cmd = str_replace('{referral_uuid}', $ref->uuid, $cmd);
                            $cmd = str_replace('{referralIP}', $ref->ip_address, $cmd);
                            $cmd = str_replace('{time}', Carbon::now()->format('H:i:s'), $cmd);
                            $cmd = str_replace('{date}', Carbon::now()->format('Y-m-d'), $cmd);
                            $cmd = str_replace('{currency}', $setting->currency, $cmd);
                            $cmdHistory = CommandHistory::create([
                                'type' => CommandHistory::TYPE_REF,
                                'payment_id' => $ref->payment_id,
                                'cmd' => $cmd,
                                'username' => $ref->username,
                                'server_id' => $server->id,
                                'status' => CommandHistory::STATUS_QUEUE,
                                'is_online_required' => false,
                            ]);
                            if ($server->method === 'websocket') {
                                if (ItemsController::sendWebsocket($cmd, $server, $ref->username, false))
                                {
                                    DB::table('ref_cmd')->where('ref_id', $ref->ref_id)->limit(1)->delete();
                                }
                            } elseif ($server->method === 'rcon') {
                                if (ItemsController::sendRcon($cmd, $server))
                                    $cmdHistory->update(['status', CommandHistory::STATUS_EXECUTED]);
                                    DB::table('ref_cmd')->where('ref_id', $ref->ref_id)->limit(1)->delete();
                            } elseif ($server->method === 'listener') {
                                if (ItemsController::sendListener($cmd, $ref->username, false, $cmdHistory->id, null))
                                    DB::table('ref_cmd')->where('ref_id', $ref->ref_id)->limit(1)->delete();
                            }
                        }
                    }
                }
            }

            $commands_history = DB::table('commands_history')
                ->join('servers', 'commands_history.server_id', '=', 'servers.id')
                ->where([
                    ['commands_history.status', '=', CommandHistory::STATUS_QUEUE],
                    ['commands_history.executed_at', '<=', $MySQL_DATATIME],
                ])
                ->select(
                    'servers.*',
                    'commands_history.id AS cmdHistoryId',
                    'commands_history.cmd',
                    'commands_history.username',
                    'commands_history.is_online_required',
                    'commands_history.package_name'
                )
                ->get();

            foreach ($commands_history as $command_history) {
                if ($command_history->method === 'websocket') {
                    ItemsController::sendWebsocket($command_history->cmd, $command_history, $command_history->username, boolval($command_history->is_online_required));
                } elseif ($command_history->method === 'rcon') {
                    if(ItemsController::sendRcon($command_history->cmd, $command_history))
                        CommandHistory::where('id', $command_history->cmdHistoryId)->update(['status' => CommandHistory::STATUS_EXECUTED]);
                } elseif ($command_history->method === 'listener') {
                    $this->info('Executing listener command: ' . $command_history->cmd);
                    ItemsController::sendListener($command_history->cmd, $command_history->username, boolval($command_history->is_online_required), $command_history->cmdHistoryId, $command_history->package_name);
                }
            }

            // Technical variables
            $currentTime = Carbon::now();

            /* |--------------------------------------------------------------------------
             * | Package Visibility Handler
             * | This part of the code will handle the sales that are expired.
             * |--------------------------------------------------------------------------
             */
            $items = Item::where('deleted', 0)->get();
            foreach ($items as $item) {
                if ($item->publishAt !== null && $item->publishAt <= $currentTime) {
                    $item->update([
                        'active' => 1,
                        'publishAt' => null,
                    ]);
                } elseif ($item->showUntil !== null && $item->showUntil <= $currentTime) {
                    $item->update([
                        'active' => 0,
                        'showUntil' => null,
                    ]);
                }
                $this->info('Item Visibility Updated: ' . $item->name);
            }

            /* |--------------------------------------------------------------------------
             * | Sale Expiration Handler
             * | This part of the code will handle the sales that are expired.
             * |--------------------------------------------------------------------------
             */
            $salesExpired = Sale::where('is_enable', 1)
                ->where('expire_at', '<=', $currentTime)
                ->get();

            foreach ($salesExpired as $sale) {
                // Get apply type for sale
                switch ($sale->apply_type) {
                    case SaleApply::TYPE_CATEGORIES:
                        $applies = $sale->applies()->pluck('apply_id')->toArray();
                        foreach ($applies as $apply) {
                            $category = Category::where('id', $apply)
                                ->where('deleted', 0)
                                ->first();

                            if ($category) {
                                $items = $category->packages()->get();
                                foreach ($items as $item) {
                                    $item->update([
                                        'discount' => 0,
                                    ]);

                                    $this->info('Item updated: ' . $item->name);
                                }
                            }
                            $this->info('Category updated: ' . $category->name);
                        }

                        $this->info('Sale were removed from all items.');
                        break;
                    case SaleApply::TYPE_PACKAGES:
                        $applies = $sale->applies()->pluck('apply_id')->toArray();
                        foreach ($applies as $apply) {
                            $item = Item::where('id', $apply)
                                ->where('deleted', 0)
                                ->first();
                            if ($item) {
                                $item->update([
                                    'discount' => 0,
                                    'featured' => 0
                                ]);
                            }
                            $this->info('Item updated: ' . $item->name);
                        }

                        $this->info('Sale were removed from all items.');
                        break;
                    case SaleApply::TYPE_WHOLE_STORE:
                        $items = Item::where('deleted', 0)->get();
                        foreach ($items as $item) {
                            $item->update([
                                'discount' => 0,
                            ]);
                        }

                        $this->info('Sale were removed from all items.');
                        break;
                }

                $sale->is_enable = 0;
                $sale->save();

                $this->info('Sale expired: ' . $sale->name);

                // Disabling announcement if it is an advert announcement
                if ($sale->is_advert === 1) {
                    $announcement = Advert::first();
                    $announcement->update([
                        'is_index' => 0,
                    ]);
                }
            }
            $this->info('Sale expiration handler executed.');

            /* |--------------------------------------------------------------------------
             * | Sale Starting Handler
             * | This part of the code will handle the sales that are starting at the current time.
             * |--------------------------------------------------------------------------
             */
            $salesStarted = Sale::where('start_at', '<=', $currentTime)
                ->where('expire_at', '>=', $currentTime)
                ->where('processed', 0)
                ->get();

            foreach ($salesStarted as $sale) {
                // Enabling sale
                $sale->is_enable = 1;
                $sale->processed = 1;
                $sale->save();
                $this->info('Sale started: ' . $sale->name);

                // Get apply type for sale
                switch ($sale->apply_type) {
                    case SaleApply::TYPE_CATEGORIES:
                        $applies = $sale->applies()->pluck('apply_id')->toArray();
                        foreach ($applies as $apply) {
                            $category = Category::where('id', $apply)
                                ->where('deleted', 0)
                                ->first();

                            if ($category) {
                                $items = $category->packages()->get();
                                foreach ($items as $item) {
                                    $item->update([
                                        'discount' => $sale->discount,
                                    ]);

                                    $this->info('Item updated: ' . $item->name);
                                }
                            }
                            $this->info('Category updated: ' . $category->name);
                        }

                        $this->info('Sale were applied to all sale category items.');
                        break;
                    case SaleApply::TYPE_PACKAGES:
                        $applies = $sale->applies()->pluck('apply_id')->toArray();
                        foreach ($applies as $apply) {
                            $item = Item::where('id', $apply)
                                ->where('deleted', 0)
                                ->first();
                            if ($item) {
                                $item->update([
                                    'discount' => $sale->discount,
                                    'featured' => 1
                                ]);
                            }
                            $this->info('Item updated: ' . $item->name);
                        }

                        $this->info('Sale were applied to all items.');
                        break;
                    case SaleApply::TYPE_WHOLE_STORE:
                        $items = Item::where('deleted', 0)->get();
                        foreach ($items as $item) {
                            $item->update([
                                'discount' => $sale->discount,
                            ]);
                        }

                        $this->info('Sale were applied to all items.');
                        break;
                }

                $announcement = Advert::first();
                if ($sale->is_advert === 1) {
                    $announcement->update([
                        'title' => $sale->advert_title,
                        'content' => $sale->advert_description,
                        'button_name' => $sale->button_name,
                        'button_url' => $sale->button_url,
                        'is_index' => 1,
                    ]);
                }
            }
            $this->info('Sale starting handler executed.');

            /* |--------------------------------------------------------------------------
             * | Coupons Visibility Handler
             * | This part of the code will handle the coupons that need to be started or expired.
             * |--------------------------------------------------------------------------
             */
            $coupons = Coupon::where('deleted', 0)->get();
            foreach ($coupons as $coupon) {
                if ($coupon->start_at !== null && $coupon->start_at <= $currentTime) {
                    $coupon->update([
                        'active' => 1,
                        'start_at' => null,
                    ]);
                } elseif ($coupon->expire_at !== null && $coupon->expire_at <= $currentTime) {
                    $coupon->update([
                        'active' => 0,
                        'expire_at' => null,
                    ]);
                }
                $this->info('Coupon Visibility Updated: ' . $coupon->name);
            }

            /* |--------------------------------------------------------------------------
             * | Giftcard Expiration Handler
             * | This part of the code will handle the gift cards that are expired.
             * |--------------------------------------------------------------------------
             */
            $giftCards = Gift::where('deleted', 0)
                ->where('expire_at', '<=', $MySQL_DATATIME)
                ->get();
            foreach ($giftCards as $giftCard) {
                $giftCard->update([
                    'status' => 0
                ]);
            }

            /* |--------------------------------------------------------------------------
             * | Giftcard Balance Handler
             * | This part of the code will handle the gift cards that are expired.
             * |--------------------------------------------------------------------------
             */
            $giftCards = Gift::where('deleted', 0)
                ->where('end_balance', '<=', 0)
                ->get();
            foreach ($giftCards as $giftCard) {
                $giftCard->update([
                    'status' => 0
                ]);
            }

            /* |--------------------------------------------------------------------------
             * | Donation Goal Time Handler
             * | This part of the code will handle the donation goals that are expired or started.
             * |--------------------------------------------------------------------------
             */
            // Handling and logging enabling donation goals.
            $enabledDonationGoals = DonationGoal::where('is_enabled', 0)
                ->where('start_at', '<=', $currentTime)
                ->get();

            if ($enabledDonationGoals->isNotEmpty()) {
                $enabledGoalNames = $enabledDonationGoals->pluck('name')->toArray();
                $this->info('Enabling donation goals: ' . implode(', ', $enabledGoalNames));

                DonationGoal::where('is_enabled', 0)
                    ->where('start_at', '<=', $currentTime)
                    ->update(['is_enabled' => 1]);
            }

            // Handling and logging disabling donation goals.
            $expiredDonationGoals = DonationGoal::where('is_enabled', 1)
                ->where('disable_at', '<=', $currentTime)
                ->get();

            if ($expiredDonationGoals->isNotEmpty()) {
                $expiredGoalNames = $expiredDonationGoals->pluck('name')->toArray();
                $this->info('Disabling donation goals: ' . implode(', ', $expiredGoalNames));

                DonationGoal::where('is_enabled', 1)
                    ->where('disable_at', '<=', $currentTime)
                    ->update(['is_enabled' => 0]);
            }

            sleep(10);
        }
    }
}
