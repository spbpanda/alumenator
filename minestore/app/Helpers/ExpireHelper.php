<?php

namespace App\Helpers;

class ExpireHelper
{
    public static function GetPeriodValueMinutes($expireUnit, $expireValue) {
        $expirePeriodValue = 0;
        switch ($expireUnit) {
            case 1:
                $expirePeriodValue = $expireValue * 60;
                break;
            case 2:
                $expirePeriodValue = $expireValue * 60 * 24;
                break;
            case 3:
                $expirePeriodValue = $expireValue * 60 * 24 * 7;
                break;
            case 4:
                $expirePeriodValue = $expireValue * 60 * 24 * 30;
                break;
            case 5:
                $expirePeriodValue = $expireValue * 60 * 24 * 365;
                break;
        }
        return $expirePeriodValue;
    }

    public static function GetOriginPeriodValue($expireUnit, $expireValue) {
        $expirePeriodValue = 0;
        switch ($expireUnit) {
            case 1:
                $expirePeriodValue = $expireValue / 60;
                break;
            case 2:
                $expirePeriodValue = $expireValue / 60 / 24;
                break;
            case 3:
                $expirePeriodValue = $expireValue / 60 / 24 / 7;
                break;
            case 4:
                $expirePeriodValue = $expireValue / 60 / 24 / 30;
                break;
            case 5:
                $expirePeriodValue = $expireValue / 60 / 24 / 365;
                break;
        }
        return $expirePeriodValue;
    }
}
