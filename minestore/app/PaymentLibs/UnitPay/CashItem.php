<?php

namespace App\PaymentLibs\UnitPay;

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
 * Value object for paid goods
 */
class CashItem
{
    const NDS_NONE = 'none';

    const NDS_0 = 'vat0';

    const NDS_10 = 'vat10';

    const NDS_20 = 'vat20';

    /** Товар */
    const PAYMENT_OBJECT_COMMODITY = 'commodity';

    /** Подакцизный товар */
    const PAYMENT_OBJECT_EXCISE = 'excise';

    /** Работа */
    const PAYMENT_OBJECT_JOB = 'job';

    /** Услуга */
    const PAYMENT_OBJECT_SERVICE = 'service';

    /** Ставка */
    const PAYMENT_OBJECT_GAMBLING_BET = 'gambling_bet';

    /** Выигрыш */
    const PAYMENT_OBJECT_GAMBLING_PRIZE = 'gambling_prize';

    /** Лотерейный билет */
    const PAYMENT_OBJECT_LOTTERY = 'lottery';

    /** Выигрыш лотереи */
    const PAYMENT_OBJECT_LOTTERY_PRIZE = 'lottery_prize';

    /** Результаты интеллектуальной деятельности */
    const PAYMENT_OBJECT_INTELLECTUAL_ACTIVITY = 'intellectual_activity';

    /** Платёж */
    const PAYMENT_OBJECT_PAYMENT = 'payment';

    /** Агентское вознаграждение */
    const PAYMENT_OBJECT_AGENT_COMMISSION = 'agent_commission';

    /** Составной предмет расчёта */
    const PAYMENT_OBJECT_COMPOSITE = 'composite';

    /** Иной предмет расчёта */
    const PAYMENT_OBJECT_ANOTHER = 'another';

    const PAYMENT_OBJECT_PROPERTY_RIGHT = 'property_right';

    const PAYMENT_OBJECT_NON_OPERATING_GAIN = 'non-operating_gain';

    const PAYMENT_OBJECT_INSURANCE_PREMIUM = 'insurance_premium';

    /** Налог с продажи */
    const PAYMENT_OBJECT_SALES_TAX = 'sales_tax';

    /** Курортный сбор */
    const PAYMENT_OBJECT_RESORT_FEE = 'resort_fee';

    /** 100% предоплата */
    const PAYMENT_METHOD_PREPAYMENT_FULL = 'full_prepayment';

    /** Частичная предоплата */
    const PAYMENT_METHOD_PREPAYMENT = 'prepayment';

    /** Аванс */
    const PAYMENT_METHOD_ADVANCE = 'advance';

    /** Полный расчёт */
    const PAYMENT_METHOD_PAYMENT_FULL = 'full_payment';

    private $name;

    private $count;

    private $price;

    private $nds;

    private $type;

    private $paymentMethod;

    /**
     * @param  string  $name
     * @param  int  $count
     * @param  float  $price
     * @param  string  $nds
     * @param  string  $type
     * @param  string  $paymentMethod
     */
    public function __construct(
        $name,
        $count,
        $price,
        $nds = self::NDS_NONE,
        $type = self::PAYMENT_OBJECT_COMMODITY,
        $paymentMethod = self::PAYMENT_METHOD_PREPAYMENT_FULL
    ) {
        $this->name = $name;
        $this->count = $count;
        $this->price = $price;
        $this->nds = $nds;
        $this->type = $type;
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getNds()
    {
        return $this->nds;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }
}
