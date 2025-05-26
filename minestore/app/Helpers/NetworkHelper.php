<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Request;

class NetworkHelper
{
    /**
     * Get or validate an IP address.
     *
     * @param string|null $ip Optional IP address to validate
     * @return string|null The validated public IP address or null if invalid or not found
     */
    public static function getIp(?string $ip = null): ?string
    {
        if ($ip !== null) {
            $ip = trim($ip);

            if ($ip === '127.0.0.1') {
                return $ip;
            }

            return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) ? $ip : null;
        }

        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ];

        foreach ($headers as $key) {
            if ($ip = Request::server($key)) {
                foreach (explode(',', $ip) as $address) {
                    $address = trim($address);
                    if (filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                        return $address;
                    }
                }
            }
        }

        return null;
    }
}
