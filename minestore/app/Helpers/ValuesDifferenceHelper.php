<?php

namespace App\Helpers;

class ValuesDifferenceHelper
{
    public static function getPercentageDifference(float|string $value1, float|string $value2): float|int
    {
        $value1 = (float)$value1;
        $value2 = (float)$value2;

        if ($value1 == 0 && $value2 == 0) {
            return 0;
        }

        if ($value2 == 0) {
            return 100;
        }

        return round((($value1 - $value2) / $value2) * 100, 2);
    }
}
