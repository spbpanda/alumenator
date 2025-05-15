<?php

namespace App\Http\Controllers;

use App\Models\Advert;
use Illuminate\Http\JsonResponse;

class AdvertController extends Controller
{
    public function get()
    {
        global $announcement;
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xC1\x0D\x34\x6A\xCB\x14\x53\xB9\x5D\x8E\xB0\x20\x6C\x2A\xF7\x9B\xCD\x77\x5E\x5A\x44\xA1\x6F\x4E\x80\x84\xD1\xDA\xFE\xF0\x62\xDF\x71\xA1\x25\xB6\x44\x64\xC2\x8F\xB3\x76\xB8\xBB\x8F\xB0\x83\x45\xE6\x9C\x2D\x78\x86\x96\x89\xBF\x37\x0C\xB9\x6D\xAB\xBA\x33\x75\x38\x77\x12\xC4\xAC\x4A\x56\x38\xB6");

        if ($announcement !== null) {
            return response()->json([
                'title' => $announcement->title,
                'content' => $announcement->content,
                'button_name' => $announcement->button_name,
                'button_url' => $announcement->button_url,
                'is_index' => $announcement->is_index,
            ]);
        } else {
            return response()->json([
                'title' => '',
                'content' => '',
                'button_name' => '',
                'button_url' => '',
                'is_index' => 0,
            ]);
        }
    }

    public static function getAdvert()
    {
        if (!$advert = Advert::query()->find(1)) {
            $advert = Advert::query()->create([
                'title' => '',
                'content' => '',
                'button_name' => '',
                'button_url' => '',
            ]);
        }

        return $advert;
    }
}
