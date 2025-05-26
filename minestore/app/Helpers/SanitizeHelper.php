<?php

namespace App\Helpers;

use App\Facades\PaynowManagement;
use App\Models\PnSetting;
use Illuminate\Support\Str;

class SanitizeHelper
{
    public static function makeSlug(string $input): string
    {
        return strtolower(
            trim(
                preg_replace('/[^a-z0-9]+/i', '-', $input),
                '-'
            )
        );
    }

    public static function generateCategory(): string
    {
        $data = self::buildDataForCategory();

        return PaynowManagement::createTag($data);
    }

    public static function buildDataForCategory(): array
    {
        $name = "variable-minestore-" . rand(1000, 9999);

        return [
            'slug' => self::makeSlug($name) . '-' . Str::random(4),
            'name' => $name,
            'description' => '<p>This is a variable category for all the variables available in the store.</p>',
            'enabled' => true,
        ];
    }

    public static function ensureVariableTagCategory(): string
    {
        $pn_settings = PnSetting::first();
        if (!$pn_settings->variable_tag_id) {
            $category = self::generateCategory();

            $pn_settings->variable_tag_id = $category;
            $pn_settings->save();
        } else {
            if (PaynowManagement::getTag($pn_settings->variable_tag_id) === null) {
                $category = self::generateCategory();

                $pn_settings->variable_tag_id = $category;
                $pn_settings->save();
            }

            $category = $pn_settings->variable_tag_id;
        }

        return $category;
    }

    public static function createSlug(string $name): string
    {
        $slug = Str::slug($name, '-');

        if (empty($slug)) {
            $slug = 'product-' . Str::lower(Str::random(6));
        }

        $randomDigits = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);

        return $slug . '-' . $randomDigits;
    }
}
