<?php

namespace App\Helpers;

class QuantityHelper
{
    public static function GetPeriodValueMinutes($periodUnit, $quantityValue) {
        $quantityPeriodValue = 0;
        switch ($periodUnit) {
            case -1:
                $quantityPeriodValue = $quantityValue;
                break;
            case 0:
                $quantityPeriodValue = $quantityValue;
                break;
            case 1:
                $quantityPeriodValue = $quantityValue * 60;
                break;
            case 2:
                $quantityPeriodValue = $quantityValue * 60 * 24;
                break;
            case 3:
                $quantityPeriodValue = $quantityValue * 60 * 24 * 7;
                break;
            case 4:
                $quantityPeriodValue = $quantityValue * 60 * 24 * 30;
                break;
            case 5:
                $quantityPeriodValue = $quantityValue * 60 * 24 * 365;
                break;
        }
        return $quantityPeriodValue;
    }

    public static function GetOriginPeriodValue($periodUnit, $quantityValue) {
        $quantityPeriodValue = 0;
        switch ($periodUnit) {
            case -1:
                $quantityPeriodValue = $quantityValue;
                break;
            case 0:
                $quantityPeriodValue = $quantityValue;
                break;
            case 1:
                $quantityPeriodValue = $quantityValue / 60;
                break;
            case 2:
                $quantityPeriodValue = $quantityValue / 60 / 24;
                break;
            case 3:
                $quantityPeriodValue = $quantityValue / 60 / 24 / 7;
                break;
            case 4:
                $quantityPeriodValue = $quantityValue / 60 / 24 / 30;
                break;
            case 5:
                $quantityPeriodValue = $quantityValue / 60 / 24 / 365;
                break;
        }
        return $quantityPeriodValue;
    }
}
