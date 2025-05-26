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

    public static function getStringUnitValue($expireUnit): string
    {
        $unit = match ($expireUnit) {
            1 => 'minute',
            2 => 'hour',
            3 => 'day',
            4 => 'month',
            5 => 'year',
            default => null,
        };

        if (in_array($unit, ['minute', 'hour']) || $unit === null) {
            return 'day';
        }

        return $unit;
    }
}
