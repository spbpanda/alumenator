<?php

namespace App\PaymentLibs\UnitPay;

use InvalidArgumentException;
use UnexpectedValueException;

/**
 * UnitPay Payment Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category        UnitPay
 *
 * @version         2.0.4
 *
 * @author          UnitPay
 * @copyright       Copyright (c) 2015 UnitPay
 * @license         http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * EXTENSION INFORMATION
 */

/**
 * Payment method UnitPay process
 *
 * @author      UnitPay <support@unitpay.ru>
 */
class UnitPay
{
    private $supportedCurrencies = ['EUR', 'UAH', 'BYR', 'USD', 'RUB'];

    private $supportedUnitpayMethods = ['initPayment', 'getPayment'];

    private $requiredUnitpayMethodsParams = [
        'initPayment' => ['desc', 'account', 'sum'],
        'getPayment' => ['paymentId'],
    ];

    private $supportedPartnerMethods = ['check', 'pay', 'error'];

    private $supportedUnitpayIp = [
        '31.186.100.49',
        '178.132.203.105',
        '52.29.152.23',
        '52.19.56.234',
        '127.0.0.1', // for debug
    ];

    private $secretKey;

    private $params = [];

    private $apiUrl;

    private $formUrl;

    public function __construct($secretKey = null)
    {
        $this->secretKey = $secretKey;
        $this->apiUrl = "https://unitpay.ru/api";
        $this->formUrl = "https://unitpay.ru/pay/";
    }

    /**
     * Create SHA-256 digital signature
     *
     * @param  array  $params
     * @param    $method
     * @return string
     */
    public function getSignature(array $params, $method = null)
    {
        ksort($params);
        unset($params['sign'], $params['signature'], $params[PHP_INT_MAX]);
        $params[] = $this->secretKey;

        if ($method) {
            array_unshift($params, $method);
        }

        return hash('sha256', implode('{up}', $params));
    }

    /**
     * Return IP address
     *
     * @return string
     */
    protected function getIp()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Get URL for pay through the form
     *
     * @param  string  $publicKey
     * @param  string|float|int  $sum
     * @param  string  $account
     * @param  string  $desc
     * @param  string  $currency
     * @param  string  $locale
     * @return string
     */
    public function form($publicKey, $sum, $account, $desc, $currency = 'RUB', $locale = 'ru')
    {
        $vitalParams = [
            'account' => $account,
            'currency' => $currency,
            'desc' => $desc,
            'sum' => $sum,
        ];

        $this->params = array_merge($this->params, $vitalParams);

        if ($this->secretKey) {
            $this->params['signature'] = $this->getSignature($vitalParams);
        }

        $this->params['locale'] = $locale;

        return $this->formUrl.$publicKey.'?'.http_build_query($this->params);
    }

    /**
     * Set customer email
     *
     * @param  string  $email
     * @return UnitPay
     */
    public function setCustomerEmail($email)
    {
        $this->params['customerEmail'] = $email;

        return $this;
    }

    /**
     * Set customer phone number
     *
     * @param  string  $phone
     * @return UnitPay
     */
    public function setCustomerPhone($phone)
    {
        $this->params['customerPhone'] = $phone;

        return $this;
    }

    /**
     * Set list of paid goods
     *
     * @param  CashItem[]  $items
     * @return UnitPay
     */
    public function setCashItems($items)
    {
        $this->params['cashItems'] = base64_encode(
            json_encode(
                array_map(function ($item) {
                    /** @var CashItem $item */
                    return [
                        'name' => $item->getName(),
                        'count' => $item->getCount(),
                        'price' => $item->getPrice(),
                        'nds' => $item->getNds(),
                        'type' => $item->getType(),
                        'paymentMethod' => $item->getPaymentMethod(),
                    ];
                }, $items)));

        return $this;
    }

    /**
     * Set callback URL
     *
     * @param  string  $backUrl
     * @return UnitPay
     */
    public function setBackUrl($backUrl)
    {
        $this->params['backUrl'] = $backUrl;

        return $this;
    }

    /**
     * Call API
     *
     * @param    $method
     * @param  array  $params
     * @return object
     *
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function api($method, $params = [])
    {
        if (! in_array($method, $this->supportedUnitpayMethods)) {
            throw new UnexpectedValueException('Method is not supported');
        }

        if (isset($this->requiredUnitpayMethodsParams[$method])) {
            foreach ($this->requiredUnitpayMethodsParams[$method] as $rParam) {
                if (! isset($params[$rParam])) {
                    throw new InvalidArgumentException('Param '.$rParam.' is null');
                }
            }
        }

        $params['secretKey'] = $this->secretKey;
        if (empty($params['secretKey'])) {
            throw new InvalidArgumentException('SecretKey is null');
        }

        $requestUrl = $this->apiUrl.'?'.http_build_query([
            'method' => $method,
            'params' => $params,
        ], null, '&', PHP_QUERY_RFC3986);

        $response = json_decode(file_get_contents($requestUrl));
        if (! is_object($response)) {
            throw new InvalidArgumentException('Temporary server error. Please try again later.');
        }

        return $response;
    }

    /**
     * Check request on handler from UnitPay
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function checkHandlerRequest()
    {
        $ip = $this->getIp();
        if (! isset($_GET['method'])) {
            throw new InvalidArgumentException('Method is null');
        }

        if (! isset($_GET['params'])) {
            throw new InvalidArgumentException('Params is null');
        }

        [$method, $params] = [$_GET['method'], $_GET['params']];

        if (! in_array($method, $this->supportedPartnerMethods)) {
            throw new UnexpectedValueException('Method is not supported');
        }

        if (! isset($params['signature']) || $params['signature'] !== $this->getSignature($params, $method)) {
            throw new InvalidArgumentException('Wrong signature');
        }

        /**
         * IP address check
         *
         * @link http://help.unitpay.ru/article/67-ip-addresses
         * @link http://help.unitpay.money/article/67-ip-addresses
         */
        if (! in_array($ip, $this->supportedUnitpayIp)) {
            throw new InvalidArgumentException('IP address Error');
        }

        return true;
    }

    /**
     * Response for UnitPay if handle success
     *
     * @param $message
     * @return string
     */
    public function getSuccessHandlerResponse($message)
    {
        return json_encode([
            'result' => [
                'message' => $message,
            ],
        ]);
    }

    /**
     * Response for UnitPay if handle error
     *
     * @param $message
     * @return string
     */
    public function getErrorHandlerResponse($message)
    {
        return json_encode([
            'error' => [
                'message' => $message,
            ],
        ]);
    }
}
