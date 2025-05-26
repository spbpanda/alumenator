<?php

namespace App\Helpers;

use GeoIp2\Database\Reader;

class GeoHelper
{
    /**
     * Get country name by IP address
     * @param string $ip
     * @return string|null
     */
    public static function getCountryNameByIp(string $ip): ?string
    {
        try {
            $reader = new Reader(base_path('GeoLite2-Country.mmdb'));
            $record = $reader->country($ip);
            return $record->country->name;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get country code by ip address
     * @param string $ip
     * @return string|null
     */
    public static function getCountryCodeByIp(string $ip): ?string
    {
        try {
            $reader = new Reader(base_path('GeoLite2-Country.mmdb'));
            $record = $reader->country($ip);
            return $record->country->isoCode;
        } catch (\Exception $e) {
            return null;
        }
    }
}
