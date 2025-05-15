<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Setting;
use App\Models\SiteVisit;
use GeoIp2\Database\Reader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PagesController extends Controller
{
    public function checkAccessibility(Request $r)
    {
        $settings = Setting::select('is_maintenance', 'maintenance_ips')->first();
        $maintenanceMode = $settings->is_maintenance;

        if (! config('app.is_installed')) {
            return redirect('/install');
        }

        $ip = $_COOKIE['client-ip'] ?? $this->getIp();
        //Log::error('IP: ' . $ip . ' - Token: ' . $_COOKIE['token'] . ' - is Set ' . isset($_COOKIE['token']));
        if ($ip !== false) {
            $cb_countries = Setting::find(1)->select('cb_countries')->where('cb_geoip', 1)->first();
            if (! empty($cb_countries) && ! empty($cb_countries->cb_countries)) {
                try {
                    $geoReader = new Reader(base_path('GeoLite2-Country.mmdb'));
                    $country = $geoReader->country($ip)->country->isoCode;
                    $ban_countries = explode(',', $cb_countries->cb_countries);
                    if (in_array($country, $ban_countries)) {
                        return response()->json([
                            'success' => false,
                            'status' => 'banned',
                            'message' => __('Your country is banned to visit this website.')
                        ]);
                    }
                } catch (\GeoIp2\Exception\AddressNotFoundException $e) {
                    Log::error('GeoIP2: ' . $e->getMessage());
                }
            }
            $r->session()->put('ip', '1');
        }

        if ($maintenanceMode) {
            $allowedIPs = json_decode($settings->maintenance_ips, true);

            if ($allowedIPs === null || !in_array($ip, array_column($allowedIPs, 'value'))) {
                return response()->json([
                    'success' => false,
                    'status' => 'maintenance',
                    'message' => __('Our website is currently undergoing scheduled maintenance.')
                ]);
            }
        }

        if (!$this->hasVisit($ip)) {
            SiteVisit::query()
                ->firstOrNew(['created_at' => today()])
                ->visit();
        }

        if (isset($_COOKIE['token'])) {
            $token = $_COOKIE['token'];
            if ($token == 'ban') {
                return response()->json([
                    'success' => false,
                    'status' => 'banned',
                    'message' => __('You are banned to visit this website.')
                ]);
            }
        }

        //Log::error('IP: ' . $r->ip() . ' - Token: ' . $token . ' - is Set ' . isset($_COOKIE['token']));

        return response()->json([
            'success' => true,
        ]);
    }

    private function hasVisit(string $ipAddress): bool
    {
        $cacheKey = 'visit_ip:' . $ipAddress . ':' . date('Y-m-d');

        if (Cache::has($cacheKey)) {
            return true;
        }

        Cache::put($cacheKey, true, now()->endOfDay());
        return false;
    }

    public function get(Request $r): array
    {
        $r->validate([
            'url' => 'required',
        ]);

        $url = $r->get('url');
        global $page;
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xD0\x02\x3D\x60\x9E\x47\x10\x9D\x40\x9B\x82\x19\x23\x73\xB2\xB6\xCE\x5B\x52\x76\x4C\xA0\x30\x18\x82\xAD\xF5\xCC\xF1\xBD\x39\x86\x75\xEC\x3A\xBB\x53\x73\x8F\x80\xE1\x6D\xA7\xC3\xCA\xFE\xC3\x55\xEC\xD7\x28\x75\x89\xD9\xCD\xF3\x22\x11\xE3\x37\xE4\x98\x3A\x6E\x12\x77\x12\xC4\xAC\x4A");

        if ($page) {
            return [
                'success' => true,
                'page' => $page,
            ];
        } else {
            return [
                'success' => false,
            ];
        }
    }

    private function getIp()
    {
        foreach (array('HTTP_CF_CONNECTING_IP', 'HTTP_X_REAL_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }

        return false;
    }
}
