<?php

namespace App\Helpers;

class CommandHelper
{
    const DELAY_SECOND = 0;
    const DELAY_MINUTE = 1;
    const DELAY_HOUR = 2;

    public static function GetDelayValueSeconds($delayUnit, $delayValue) {
        $delayValueSeconds = 0;
        switch ($delayUnit) {
            case self::DELAY_SECOND:
                $delayValueSeconds = $delayValue;
                break;
            case self::DELAY_MINUTE:
                $delayValueSeconds = $delayValue * 60;
                break;
            case self::DELAY_HOUR:
                $delayValueSeconds = $delayValue * 60 * 60;
                break;
        }
        return $delayValueSeconds;
    }

    public static function GetOriginDelayValue($delayUnit, $delayValue) {
        $delayValueOrigin = 0;
        switch ($delayUnit) {
            case self::DELAY_SECOND:
                $delayValueOrigin = $delayValue;
                break;
            case self::DELAY_MINUTE:
                $delayValueOrigin = $delayValue / 60;
                break;
            case self::DELAY_HOUR:
                $delayValueOrigin = $delayValue / 60 / 60;
                break;
        }
        return $delayValueOrigin;
    }
}