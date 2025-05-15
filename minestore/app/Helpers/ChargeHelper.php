<?php

namespace App\Helpers;

class ChargeHelper
{
    const CHARGE_DAY = 1;
    const CHARGE_WEEK = 2;
    const CHARGE_MONTH = 3;
    const CHARGE_YEAR = 4;

    public static function GetChargeDays($chargeUnit, $chargeValue) {
        $chargeDays = 0;
        switch ($chargeUnit) {
            case self::CHARGE_DAY:
                $chargeDays = $chargeValue;
                break;
            case self::CHARGE_WEEK:
                $chargeDays = $chargeValue * 14;
                break;
            case self::CHARGE_MONTH:
                $chargeDays = $chargeValue * 30;
                break;
            case self::CHARGE_YEAR:
                $chargeDays = $chargeValue * 365;
                break;
        }
        return $chargeDays;
    }
}