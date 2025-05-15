<?php

namespace App\Console\Commands;

use App\Models\Currency;
use App\Models\Payment;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class CurrencyUpdate extends Command
{
    protected $signature = 'currency:update';

    protected $description = 'Currency update use API minestorecms.com';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $jsonData = file_get_contents('https://minestorecms.com/api/currency/'.config('app.LICENSE_KEY'));
        if (! empty($jsonData) && $curData = json_decode($jsonData, true)) {
            foreach ($curData as $currency) {
                if ($currency['name'] == 'VND') {
                    Currency::updateOrCreate(['name' => $currency['name']], ['value' => '25365']);
                } elseif ($currency['name'] == 'ARS') {
                    Currency::updateOrCreate(['name' => $currency['name']], ['value' => '1200']);
                } else {
                    Currency::updateOrCreate(['name' => $currency['name']], ['value' => $currency['value']]);
                }
            }
            $this->info('Currency updated successfully');
        }

        $date = Carbon::now();
        $date->hour = 0;
        $date->minute = 0;
        $date->second = 0;

        $today = DB::table('carts')
            ->join('payments', 'payments.cart_id', '=', 'carts.id')
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED])
            ->where('payments.created_at', '>', $date)
            ->select(DB::raw('IFNULL(SUM(carts.price),0) as price'))
            ->get();
        if ($today->isNotEmpty()) {
            $today = round($today[0]->price, 2);
        } else {
            $today = 0;
        }

        $date->day = 1;
        $monthly = DB::table('carts')
            ->join('payments', 'payments.cart_id', '=', 'carts.id')
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED])
            ->where('payments.created_at', '>', $date)
            ->select(DB::raw('IFNULL(SUM(carts.price),0) as price'))
            ->get();
        if ($monthly->isNotEmpty()) {
            $monthly = round($monthly[0]->price, 2);
        } else {
            $monthly = 0;
        }

        $date->month = 1;
        $yearly = DB::table('carts')
            ->join('payments', 'payments.cart_id', '=', 'carts.id')
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED])
            ->where('payments.created_at', '>', $date)
            ->select(DB::raw('IFNULL(SUM(carts.price),0) as price'))
            ->get();
        if ($yearly->isNotEmpty()) {
            $yearly = round($yearly[0]->price, 2);
        } else {
            $yearly = 0;
        }

        $topPackages = DB::table('cart_items')
            ->join('carts', 'cart_items.cart_id', '=', 'carts.id')
            ->join('items', 'cart_items.item_id', '=', 'items.id')
            ->join('payments', 'payments.cart_id', '=', 'carts.id')
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED])
            ->select(DB::raw('items.name as package'),
                DB::raw('items.price'),
                DB::raw('COUNT(*) as total_records'))
            ->orderBy('total_records', 'DESC')
            ->groupBy(DB::raw('items.id'))
            ->limit(5)
            ->get();
        $system_currency = Setting::select('currency')->find(1)->currency;
        $topPackagesData = [];
        foreach ($topPackages as $topPackage) {
            $topPackagesData[] = ['name' => $topPackage->package, 'price' => $topPackage->price];
        }

        $serverCountry = Carbon::now()->getTimezone()->getName();
        $settings = Setting::select(['serverIP', 'site_name', 'lang', 'theme'])->find(1);

        // Get Minecraft Server IP (public domain ex. mc.hypixel.net)
        $minecraftServerIP = $settings->serverIP;

        // Get webstore name
        $webstoreName = $settings->site_name;

        // Get primary language
        $primaryLanguage = $settings->lang;

        // Get Theme Id
        $themeId = $settings->theme;

        $post_data = [
            'name' => $webstoreName,
            'version' => config('app.version'),
            'details' => [
                'currency' => $system_currency,
                'server_country' => $serverCountry,
                'primary_language' => $primaryLanguage,
                'minecraft_server_ip' => $minecraftServerIP,
                'today' => $today,
                'monthly' => $monthly,
                'yearly' => $yearly,
                'top_packages' => $topPackagesData,
                'total' => Payment::whereIn('status', [Payment::PAID, Payment::COMPLETED])->count(),
                'theme' => $themeId,
            ],
            'date' => Carbon::now()->format('Y-m-d H:i:s'),
        ];

        $ch = curl_init('https://minestorecms.com/api/collect/metrics/global/'.config('app.LICENSE_KEY'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_exec($ch);
        curl_close($ch);
    }
}
