<?php

namespace App\Helpers;

use App\Models\SaleApply;
use Carbon\Carbon;

class SalesHelper
{
    public static function GetStatus($sale)
    {
        if ($sale->is_enable) {
            return 0;
        } else if ($sale->start_at > Carbon::now()) {
            return 1;
        } else {
            return 2;
        }
    }

    public static function getApplies(array $attributes): ?array
    {
        if ($attributes['apply_type'] == SaleApply::TYPE_CATEGORIES) {
            return $attributes['apply_categories'];
        } elseif ($attributes['apply_type'] == SaleApply::TYPE_PACKAGES) {
            return $attributes['apply_items'];
        }

        return null;
    }

    public static function getApplyType(string $type): ?string
    {
        if ($type == SaleApply::TYPE_CATEGORIES) {
            return 'category';
        } elseif ($type == SaleApply::TYPE_PACKAGES) {
            return 'item';
        }

        return null;
    }
}
