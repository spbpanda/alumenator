<?php

namespace App\PaymentLibs;

/**
 * Paytm uses checksum signature to ensure that API requests and responses shared between your
 * application and Paytm over network have not been tampered with. We use SHA256 hashing and
 * AES128 encryption algorithm to ensure the safety of transaction data.
 *
 * @author     Lalit Kumar
 *
 * @version    2.0
 *
 * @link       https://developer.paytm.com/docs/checksum/#php
 */
class PaytmChecksum
{
    private static $iv = '@@@@&&&&####$$$$';

    public static function encrypt($input, $key)
    {
        $key = html_entity_decode($key);

        if (function_exists('openssl_encrypt')) {
            $data = openssl_encrypt($input, 'AES-128-CBC', $key, 0, self::$iv);
        } else {
            $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'cbc');
            $input = self::pkcs5Pad($input, $size);
            $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', 'cbc', '');
            mcrypt_generic_init($td, $key, self::$iv);
            $data = mcrypt_generic($td, $input);
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);
            $data = base64_encode($data);
        }

        return $data;
    }

    public static function decrypt($encrypted, $key)
    {
        $key = html_entity_decode($key);

        if (function_exists('openssl_decrypt')) {
            $data = openssl_decrypt($encrypted, 'AES-128-CBC', $key, 0, self::$iv);
        } else {
            $encrypted = base64_decode($encrypted);
            $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', 'cbc', '');
            mcrypt_generic_init($td, $key, self::$iv);
            $data = mdecrypt_generic($td, $encrypted);
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);
            $data = self::pkcs5Unpad($data);
            $data = rtrim($data);
        }

        return $data;
    }

    public static function generateSignature($params, $key)
    {
        if (! is_array($params) && ! is_string($params)) {
            throw new Exception('string or array expected, '.gettype($params).' given');
        }
        if (is_array($params)) {
            $params = self::getStringByParams($params);
        }

        return self::generateSignatureByString($params, $key);
    }

    public static function verifySignature($params, $key, $checksum)
    {
        if (! is_array($params) && ! is_string($params)) {
            throw new Exception('string or array expected, '.gettype($params).' given');
        }
        if (isset($params['CHECKSUMHASH'])) {
            unset($params['CHECKSUMHASH']);
        }
        if (is_array($params)) {
            $params = self::getStringByParams($params);
        }

        return self::verifySignatureByString($params, $key, $checksum);
    }

    private static function generateSignatureByString($params, $key)
    {
        $salt = self::generateRandomString(4);

        return self::calculateChecksum($params, $key, $salt);
    }

    private static function verifySignatureByString($params, $key, $checksum)
    {
        $paytm_hash = self::decrypt($checksum, $key);
        $salt = substr($paytm_hash, -4);

        return $paytm_hash == self::calculateHash($params, $salt) ? true : false;
    }

    private static function generateRandomString($length)
    {
        $random = '';
        srand((float) microtime() * 1000000);

        $data = '9876543210ZYXWVUTSRQPONMLKJIHGFEDCBAabcdefghijklmnopqrstuvwxyz!@#$&_';

        for ($i = 0; $i < $length; $i++) {
            $random .= substr($data, (rand() % (strlen($data))), 1);
        }

        return $random;
    }

    private static function getStringByParams($params)
    {
        ksort($params);
        $params = array_map(function ($value) {
            return ($value !== null && strtolower($value) !== 'null') ? $value : '';
        }, $params);

        return implode('|', $params);
    }

    private static function calculateHash($params, $salt)
    {
        $finalString = $params.'|'.$salt;
        $hash = hash('sha256', $finalString);

        return $hash.$salt;
    }

    private static function calculateChecksum($params, $key, $salt)
    {
        $hashString = self::calculateHash($params, $salt);

        return self::encrypt($hashString, $key);
    }

    private static function pkcs5Pad($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);

        return $text.str_repeat(chr($pad), $pad);
    }

    private static function pkcs5Unpad($text)
    {
        $pad = ord($text[strlen($text) - 1]);
        if ($pad > strlen($text)) {
            return false;
        }

        return substr($text, 0, -1 * $pad);
    }
}
