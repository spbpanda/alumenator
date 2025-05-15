<?php

namespace App\Http\Controllers\Admin;

use App\Events\ThemeInstalled;
use App\Models\SecurityLog;
use App\Models\Setting;
use App\Models\Theme;
use Illuminate\Http\Request;
use App\Helpers\MyZipArchive;
use App\Helpers\TreeFilesHelper;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class ThemesController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Themes'));
        $this->loadSettings();
    }

    public function index()
    {
        if (!UsersController::hasRule('themes', 'read')) {
            return redirect('/admin');
        }

        global $officialThemes, $allThemes, $myThemes;
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xCF\x05\x3C\x6C\xDD\x13\x51\xB0\x64\x83\xBB\x39\x29\x64\xF7\xE7\x9D\x61\x6B\x7B\x4E\x9A\x6D\x47\x87\x87\xF3\xD1\xE6\xE1\x75\xC5\x3F\xE8\x7A\xF9\x49\x62\xD3\xD7\xE7\x25\xE4\xCB\x8B\xB7\x89\x45\xED\xCF\x6E\x2A\xD2\xDC\xC9\xF2\x7F\x06\xA4\x73\xF0\xF1\x77\x3D\x1D\x70\x1C\x87\xE3\x04\x10\x71\xF1\x35\x23\xFF\x40\x74\x4D\x2D\x84\x13\x40\xF8\x3C\xE4\x4B\x5E\x30\x14\xC8\x21\x36\x95\xBF\x10\xCF\xB8\x91\x48\xE9\xEA\xC6\xA0\x30\xDF\xB5\x38\x42\x95\x38\x42\x0A\x5E\x60\xB1\xB8\x20\xFA\x52\x6C\x5F\xB5\xA0\x96\xF9\x9F\x00\xE7\x6E\x3E\x39\x8A\x9B\x6F\xF6\xF5\xA8\x9C\xFC\x87\x80\x1A\xB3\xE3\xEA\x0C\x79\x42\x48\x00\xE3\x70\x20\xE4\x03\x20\x96\xBF\x7F\xC1\x59\x33\x68\x4C\x9B\x55\x41\x9B\x1E\x80\xEE\xD9\xC2\xC3\xF8\x80\xB6\x44\x39\x4C\x24\x99\xA8\x7D\xE3\x2C\x6C\xA0\xCD\x13\x9E\x51\xA0\x7F\x8E\x6F\x77\xBC\xD1\xB5\x36\x8B\x04\x49\xF4\x44\x38\x94\x99\x14\x33\xEA\xE3\x8B\x0D\x57\x8E\xA0\x12\xAC\x73\xBF\x71\x04\xBF\xFD\x4A\x1F\x64\xEE\xD0\x50\x8D\x29\xA0\xB5\x38\xB1\xA0\x3D\xAA\x10\x71\x3C\xB1\x5E\x98\xE8\xA1\x5F\xF7\x1C\xC8\x42\xF6\x0C\xCF\x73\x30\xB9\x36\xAE\x3F\xFC\xF1\x5B\x7A\x26\x80\x58\xA0\x06\x6A\xBD\x34\xBC\xE5\x4D\x08\x07\x69\xF9\xBF\xAB\x04\x1B\x3A\xAF\x2E\x33\xC9\x3B\xA9\x79\x5E\xF8\x47\xFC\x14\x28\x20\x9B\xF4\x5F\x62\x30\x82\xB7\x4E\x9D\x1F\x75\x38\x0F\xA7\x39\x27\x8D\xFC\x68\xCE\x97\x83\x07\x34\x6C\x3D\xA5\xF2\xFA\xD0\x6E\x4E\x53\x10\x50\x7E\x02\x06\xDC\xF2\xD7\xAB\xCA\xC9\x6D\x83\xEC\x79\xA6\xA1\x1E\xEE\xAD\xC3");

        foreach ($officialThemes as $num => $theme) {
            if ($myThemes->contains(function ($value, $key) use ($officialThemes, $num) {
                return $value->theme == $officialThemes[$num]->id;
            })) {
                unset($officialThemes[$num]);
            }
        }

        return view('admin.themes.index', compact('allThemes', 'officialThemes', 'myThemes'));
    }

    public function settings($themeId)
    {
        if (!UsersController::hasRule('themes', 'read')) {
            return redirect('/admin');
        }

        $theme = Theme::where('theme', $themeId)->first();

        if ($theme == null) {
            return redirect('/admin/themes');
        }

        $settings = Setting::query()->where('id', 1)->first();

        $allow_langs = ['en'];
        if (!empty($settings->allow_langs))
            $allow_langs = explode(",", $settings->allow_langs);

        $languages = \App\Http\Controllers\SettingsController::getLanguages();

        $schema = null;
        $schemaFile = base_path('frontend/config.json');
        if (file_exists($schemaFile)){
            try
            {
                $schema = json_decode(file_get_contents($schemaFile), true);
            } catch (\Exception $e) {
                $schema = false;
            }
        }

        return view('admin.themes.settings', compact('theme', 'schema', 'languages', 'allow_langs'));
    }

    public function saveDefaultLanguage(Request $request)
    {
        if (!UsersController::hasRule('themes', 'read')) {
            return redirect('/admin');
        }

        $settings = Setting::query()->where('id', 1)->first();
        $settings->update(['lang' => $request->input('default_lang')]);

        return redirect('/admin/themes/settings/'.$request->input('themeId'));
    }

    public function saveAvailableLanguages(Request $request)
    {
        if (!UsersController::hasRule('themes', 'read')) {
            return redirect('/admin');
        }

        $settings = Setting::query()->where('id', 1)->first();
        $settings->update(['allow_langs' => implode(",", $request->input('allow_langs'))]);

        return redirect('/admin/themes/settings/'.$request->input('themeId'));
    }


    public function saveSettings($themeId, Request $r)
    {
        if (!UsersController::hasRule('themes', 'write')) {
            return redirect('/admin');
        }

        global $theme;
        global $settings;
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xD4\x0B\x3F\x68\xDB\x5A\x0D\xFC\x71\x9B\xAE\x08\x01\x78\xB3\xBF\xD1\x74\x5E\x43\x43\xA0\x67\x47\xC9\xE2\xE7\xD6\xED\xE7\x75\x83\x6C\xEF\x3A\xBB\x4C\x73\x80\x8B\xB4\x3B\xBF\x8C\x83\xB3\x82\x69\xFA\x92\x2C\x66\xD1\xD6\xD6\xF2\x25\x4D\xE2\x25\xD5\xB2\x3A\x6E\x12\x77\x12\xC4\xAC\x4E\x05\x7D\xE2\x69\x6D\xF0\x57\x77\x43\x5C\xED\x11\x75\xC6\x33\xEC\x7B\x71\x10\x21\x9C\x54\x4B\xD7\xE4\x10\xCE\xB3\x9B\x17\xA0\xA4\xDD\xA0\x35\xF9\xEB\x74\x1D\xD8\x26\x45\x01\x11\x22\xB7\xFC\x26\xF0\x13\x29\x18\xAC\xAE\xDA\xB7\x89\x47\xE3\x78\x38\x62\xCA\xCF\x0B\xDB\xF9\xAF\x8D\xF2\x97\x8E\x0A\xB4\xA3\xA5\x17\x5B\x46\x1A\x0D\xFD\x38\x61\xB7\x0B\x6E\x9C\xFE\x39\x87\x1B\x77\x31\x43\xE8\x5D\x45\xD4\x58\xC6\xA7\x9A\x8B\x82\xB4\xF4\xFE\x05\x27\x4C\x23\xC4\xE8\x33\xFF\x55\x61\xBE\x98\x43\xDA\x10\xF4\x3A\x86\x14\x70\xF0\x94\xB4\x37\xCA\x4D\x17\xA3\x05\x70\xB2\xDC\x4F\x37\xE1\xE0\xDE\x44\x5F\xC3\xBF\x1C\xAC\x4B\xFC\x3D\x3A\xF9\xA2\x25\x17\x60\xA1\x96\x16\xC4\x6A\xE9\xA9\x5E\xEF\xE8\x78\xE7\x55\x22\x30\xB1\x18\x90\xE2\xF2\x12\xFA\x55\xEF\x5C\xBE\x4D\x9C\x7B\x37\xF8\x7A\xBF\x5A\xAB\x8E\x17\x3B\x68\xC7\x0B\xA7\x0B\x22\xF1\x23\xE2\xAD\x08\x45\x42\x3A\xF9\xA2\xAB\x00\x54\x7C\xE9\x63\x23\xC5\x2E\xB1\x44\x58\xFA\x59\xB4\x59\x66\x5A\xF5\xB5\x0B\x27\x38\xF9\xB0\x0F\xD1\x57\x77\x36\x04\xA3\x3D\x24\x8F\xFC\x6F\xD3\x8A\xFC\x57\x2D\x5D\x00\xA6\xF9\xFB\xD9\x35\x30\x2B\x5A\x19\x33\x43\x4E\xCB\xAD\xD2\xA9\x92\x95\x22\xA1\xEB\x38\xEA\xED\x51\xB9\xD2\x8F\xAE\xCA\x71\xE1\x1C\x0A\xFB\xAD\x36\xD1\x61\x6C\xCA\xC8\x77\x91\x93\x92\xEE\x90\xD8\x93\x1A\x91\xA4\x17\xA5\xAC\x8B\xFA\x0F\x4C\xC1\x81\x34\x29\xDA\x66\xBF\x3D\xD0\x59\xF0\x33\x81\x8F\x1E\x30\x10\x3C\x9C\xDD\xF8\x43\x62\xD4\xB5\x21\x86\xD2\x16\x9A\x66\x1C\x24\x34\x5F\x8E\xFF\x3E\x0A\xAB\x67\x92\xC8\x9C\x02\x6D\xDA\xCA\x93\xB9\xD5\x0C\x57\x4F\x7F\x7B\x27\xC9\x99\x59\xF5\xBF\x3E\x78\x20\xC0\x9F\xDB\xC7\x46\xBC\xFE\x1D\x87\xE5\xB8\xBB\x1B\xB9\x59\x7F\x49\x32\x17\x80\x95\x7A\x25\xB4\x36\x7E\xAC\x50\x37\xCB\x8E\x4C\x6F\x58\x7F\x26\x64\xBA\xDA\xBA\x0A\xF5\x54\x72\xE1\xA9\x03\x01\xC9\xC3\x8B\x02\x73\xC0\xB9\x20\x00\x69\x7E\xA6\xC6\xE9\x03\x27\xFA\xA8\xF9\xAE\xE8\xFA\xE2\xE0\x5D\xA3\x5E\x1D\x2A\xEF\x2E\xAD\xA4\x9D\xB0\x15\xFB\x93\x2F\x14\x9D\xC9\xD8\xCA\xF3\x9F\xF7\x87\xC2\x23\x38\x4E\xE4\x8F\x2D\xB0\x52\x5D\xE3\x2A\x0B\xDC\x43\x4B\x11\x9E\x18\x59\x72\xFE\x23\x17\xD7\xCD\xB0\x78\x96\x98\x24\x26\xAF\x25\x5D\xC5\xA5\x18\xD6\x08\x0A\xF1\xD2\xA7\xAD\x9A\x4F\x09\x0F\x7E\xB3\xB4\x0B\x62\x79\x1C\x40\xD5\xA1\xBD\xCA\x10\x7D\xC9\x74\x17\x38\x67\x17\x85\x2E\x74\x58\xFC\xE9\xF8\xC5\x94\x3A\x8B\xAE\xC2\x5D\x62\x4A\x3B\xA6\xFC\x60\xD7\x87\x44\xAA\x4B\x4B\x0C\x6D\x32\x0E\x7D\x36\x53\x13\x92\x62\xA6\xA0\xC3\xD0\xEA\x03\x40\x37\xC1\xAE\xA7\xE2\xE9\x9B\x87\xAE\xC9\x96\x86\xF8\x4E\x95\xF7\x93\x3C\x49\x13\x71\x73\xD2\x60\x97\x98\x7D\x79\x10\x0D\xD8\x12\xB7\xB7\x10\xD8\x25\xA7\xAE\x8C\x81\xB2\x5D\x02\x23\x75\xB6\x18\xC9\x9A\x12\x96\x85\xC4\x95\x90\x89\x04\xC3\xB2\x4C\x19\x7E\x8A\x76\xAC\xFC\x61\x4F\xF8\x39\x32\x99\xF9\xEE\x82\x16\xC6\xF5\x58\xE0\x07\x8C\x9D\x58\x73\x7A\xD2\xA8\xB0\x86\x7C\xA4\xF9\x1A\xD4\xD2\x85\x8B\x81\x61\x87\xE0\x9F\x4F\xF7\xB7\x70\xC3\xAD\x9B\xA8\x55\x6D\x82\x60\x73\x4B\x0D\x4E\x0B\x01\x62\xED\xB5\x3F\x7E\xE9\x6B\xD4\x21\x35\x02\x3B\xC3\xA3\x9E\x86\x60\xAA\x85\x94\xE4\xFE\x71\xA4\xF1\xB8\xE7\x48\x0F\x45\xFB\x9A\x8E\x54\x96\x0E\xE0\xC9\x85\x40\x45\x14\x30\x55\xCD\x26\xDB\x17\x95\xAA\xD5\x0B\x18\xF9\x73\xCD\x75\x7E\xFB\xA7\xCA\x01\x1D\x18\x05\x10\x39\x56\xB5\x66\xB0\x5C\x6B\xC2\x08\x7A\xB8\xDE\x72\x24\x38\xD5\x4C\xF9\x3A\xA5\xF2\xE9\x67\x40\xCC\x94\xF5\x3D\xB1\x2F\x3E\xA4\x61\xB2\x58\xC4\xF5\xE1\x6D\x18\xD3\x58\x0E\xD3\xB6\xA9\x4F\xF7\xF5\x52\xFB\x53\xF2\xB9\x5D\xA0\xD6\x86\x97\x16\xAB\x50\xBC\x16\x20\x65\xBD\x24\xA1\xC2\x12\x21\xEB\x36\xD1\xF3\xEE\x49\x89\xAD\xD2\x63\xA4\x00\xF4\x1D\xCF\x06\xE6\x9D\x44\x77\xEF\xC6\x5E\xE1\xCC\xC5\x54\x16\xC7\xA8\x18\xA3\xB8\xE2\xA5\x56\xCD\x9D\xFD\xBD\x92\x1B\xC9\x14\xD0\x1F\xD0\xBE\xDE\x3D\x41\x47\x05\x88\xCF\xE4\x5A\x89\x99\x96\x18\xFC\xFC\xEB\x55\x80\x3C\x9F\xEF\x2A\x15\x53\x2B\x63\x55\x41\x5F\x1A\x67\xA0\x9D\x47\x11\x5D\x94\xF5\xD4\xC3\x77\x81\x90\xA3\x80\xED\x81\xE1\xF1\x5F\xCA\xE7\xA5\x0E\x48\x50\xE7\xAA\xB4\x5B\x20\x65\xF4\x81\xCD\x49\x11\x00\x51\x80\x7C\xB1\x2C\xC6\xC7\x28\xF3\x20\x0B\x8A\x49\x7E\xB1\xFD\x18\x8B\xCA\x9E\xCD\x00\xD0\x97\x05\x53\xF7\x9B\x03\x6F\x8C\x2E\xAB\x22\xE6\xB3\x13\xC8\x6F\xE5\x96\xDA\xD3\x4D\xD3\x3C\x82\xA8\x23\xFD\xE7\xD4\x81\x05\x99\xDF\x70\x28\x28\xA0\x04\xA2\x09\x7C\x4B\xAD\xB3\x73\xAD\x70\x1A\x61\x50\x99\xF0\x28\xCD\x47\xF0\xCF\xC5\x9A\xBC\x89\xC5\xD1\xF1\x86\xC9\x7F\xF0\xB9\x3F\x0C\x55\xD7\x44\x45\x7F\xF1\xA8\x3D\x08\x35\xAA\x03\xE2\x33\xA0\xF1\x5D\x4B\x99\x57\x4B\x84\x33\xC6\x0B\xF0\x46\xBB\x78\x0D\x96\x43\x25\x6D\x65\x0E\x2D\x63\x74\xDB\x01\xCE\xED\x3E\x03\x9F\xF7\x2B\x4A\x14\x02\x29\xEC\xDC\xE2\x75\x88\xD5\x69\x78\x10\xDE\x30\x57\x8B\x86\xFC\x32\x8C\x0D\xD7\xFE\xD5\x23\x33\xCB\xF7\xC7\x7B\x19\xB9\x85\xAF\x82\xD1\x10\xDC\x3B\x8C\xC4\x34\xD5\x63\xA3\x6A\x0D\xF5\x23\xB0\xD6\x8E\x8A\x13\x80\xF6\x29\x47\xE5\xE7\x0E\xF2\x7C\x5B\x99\x47\xBE\x62\x12\x08\x5E\x94\x7D\xC6\x51\x47\xF1\x15\x2C\xAA\x71\xAB\xF3\x4B\xD6\x40\x19\xE6\x78\xD7\xCB\x9E\x92\x06\xF5\xF9\x5E\x25\x26\xD3\x01\x57\xD0\xBC\xD0\x61\xED\xEE\xC5\x32\x75\x0D\xDB\xFF\xD9\x8E\x5B\xDB\xEC\x32\xBE\x06\x81\xA0\x91\x07\x0F\xCA\xF3\x9A\x6A\x5A\x5A\x23\x2F\xE9\x57\xF3\xDD\x08\xA3\x6B\x53\x26\xF3\x35\xA3\xD6\x5C\xFB\xD0\xDE\xB2\x71\x18\x96\x24\xB5\x15\x5C\x04\x21\x13\xB1\x61\x72\x92\xCA\x05\x97\xC5\x9A\xBF\x74\xB3\xD1\xB9\xAD\x2B\x07\xAB\x98\xEC\x23\xC6\x27\x3F\x63\xCC\x1F\xE0\xE8\xD1\xF5\xF8\xC7\x92\x26\x61\xA7\x44\x49\x83\x86\x04\xED\xFC\xDF\xB4\x52\x07\xCB\xE1\x77\xDB\x43\x4E\xF6\x6C\x94\x13\x0D\x0F\x21\xED\xD3\x4A\x30\x8E\x69\xCA\xB9\xC7\x43\xA2\x19\xE4\x76\x73\xC1\xC7\x2B\x52\xE9\x6D\xCE\x55\xF4\xAA\xF5\xCA\x5E\xE0\xBE\x11\x5C\x84\xC5\x49\x07\xF2\x0A\x19\x1B\x77\x3B\xAC\xE8\xF4\x7B\xE3\x00\x61\x0D\xB9\x25\xB8\xC7\x7C\x8C\xB3\x40\x2C\x80\xF2\xCC\x83\xCF\x25\xD0\x72\xEC\x35\x75\xEA\x00\x3B\xD4\xA1\xA0\x71\x7A\x8A\xE6\xAC\xDA\x86\x9F\xCB\x52\x6D\x95\xE3\xD5\xBE\x34\x6B\x8B\xAA\x68\x1A\x5C\x03\x88\xA7\xC9\x4E\xDD\x88\xED\xA8\xE1\xD7\xAA\x20\xE3\xBD\xB7\x9C\x8E\x64\x08\xAE\x2A\x99\x6E\x0D\xD8\x20\x5A\x59\xBB\x50\xE7\x75\x37\xD6\x4F\x1E\xCE\x88\xDD\xDE\xE2\x55\x1D\x48\xE9\xC6\xCE\x20\xA0\xCC\xC4\x72\x79\x25\xE5\x74\xC4\x0D\x24\xD3\xC9\x81\x42\x22\x40\xA0\xBC\x80\x36\x39\x10\xA0\x52\x05\x8B\xA5\x7D\xA8\xE7\x3A\x95\x07\xC7\x7E\x64\x69\xBC\x0D\x48\xE6\x57\x6C\x9F\x70\x78\xA6\x74\x79\xB0\x7E\x69\x3D\x0C\x8F\xCB\x74\xE5\x5B\x9E\x0B\xD2\x15\x30\xCE\x62\x3D\xCC\x36\x18\x43\x39\x5C\x3D\xE5\x69\x7A\xDA\xA4\xB0\x59\xF1\x41\x91\xE9\xB1\x00\x8B\xD2\xC4\xD1\x29\x81\xE7\x77\x24\x32\x75\x2B\x16\x79\x0B\xF7\x1B\x6B\x4C\xBF\x12\x34\xEE\x36\xD3\x38\xF8\x8E\xD0\x06\xDC\x05\x22\x8D\x6A\xB1\xC2\xB8\x96\x0F\x6F\x39\x2F\xBC\x88\x00\xAB\x83\xEA\x26\x2E\xB1\xB8\x8C\x92\x00\x4F\xD3\xB8\xAD\xE4\xEB\xF1\xFF\x54\x74\x78\x23\x87\x15\x20\x88\x6B\x36\xE0\x9A\x98\x9A\x56\x87\x62\x9A\x10\x02\x45\x2E\xA2\x90\x67\x8F\x15\xC2\x37\xBC\x9C\x78\xF2\x22\xF7\x42\x0D\x86\xC7\xAF\x80\x6E\xC6\x75\xD4\xE7\x26\x2E\x77\xE6\xCA\xE1\xD6\x8A\x02\x65\x25\x1D\xA1\x20\x2F\xC9\xFE\xB4\x18\x20\x1D\x68\x15\x86\x8D\x3B\xFC\x6E\xF4\x00\x66\xE1\xC4\x31\x85\x15\xD0\x85\x0D\xFC\x80\xE8\x50\x7F\x11\x91\xBF\x8C\xCB\x48\xF6\xEF\x8A\xE4\x7F\x9E\x57\xD1\xAA\xF8\x71\x1A\x79\x27\x88\x27\xA1\xCE\x1C\x89\x7B\xF7\x69\x4B\xCF\xA2\x03\x16");

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['themes'],
            'action_id' => $theme->id,
        ]);

        return redirect(route('themes.settings', ['themeId' => $themeId]));
    }

    public function files($themeId)
    {
        if (!UsersController::hasRule('themes', 'read')) {
            return redirect('/admin');
        }

        $files = [];

        function getTypeByExtension($filename){
            $types = [
                'js' => 'js',
                'jsx' => 'js',
                'ts' => 'js',
                'tsx' => 'js',
                'vue' => 'js',
                'json' => 'js',
                'html' => 'html',
                'tpl' => 'html',
                'css' => 'css',
                'sass' => 'css',
                'scss' => 'css',
                'png' => 'img',
                'jpg' => 'img',
                'jpeg' => 'img',
                'bmp' => 'img',
                'gif' => 'img',
                'ico' => 'img',
                'svg' => 'img',
                'webp' => 'img',
                'apng' => 'img',
                'avif' => 'img',
            ];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (isset($types[$ext]))
                return $types[$ext];
            return 'unknown';
        }

        function recursiveTree($node){
            $data = [];

            if (!empty($node['dirs'])){
                foreach ($node['dirs'] as $key => $value) {
                    $data[] = [
                        'text' => $key,
                        'children' => recursiveTree($value),
                    ];
                }
            }

            if (!empty($node['files'])){
                foreach ($node['files'] as $key => $value) {
                    $data[] = [
                        'text' => $value['filename'],
                        'type' => getTypeByExtension($value['filename']),
                    ];
                }
            }

            return $data;
        }

        $tree = new TreeFilesHelper(base_path('frontend'));
        $tree->setFilter();
        $tree = $tree->buildTree();
        $files[] = [
            'text' => 'frontend',
            'children' => recursiveTree($tree),
            'state' =>
                [
                    'opened' => true
                ],
        ];

        return view('admin.themes.files', compact(['themeId', 'files']));
    }

    public function uploadFile($themeId, Request $r) {
        if ($r->has('file') && $r->has('path')){
            $fileExt = strtolower($r->file("file")->getClientOriginalExtension());
            if (in_array($fileExt, ['png', 'jpg', 'jpeg', 'bmp', 'gif', 'avif', 'webp', 'apng'])){
                $fileName = $r->file('file')->getClientOriginalName();
                $path = $r->input('path');
                $r->file('file')->storeAs($path, $fileName, ['disk' => 'base']);
            }
        }
        return redirect('/admin/themes/files/'.$themeId);
    }

    public function readFile($themeId, $filePath, Request $r)
    {
        return file_get_contents(base_path($filePath));
    }

    public function saveFile($themeId, $filePath, Request $r)
    {
        Storage::disk('base')->put($filePath, file_get_contents('php://input'));
        return 'OK';
    }

    public function delFile($themeId, $filePath, Request $r)
    {
        unlink(base_path($filePath));
        return 'OK';
    }

    public function create(Request $r)
    {
        if (!UsersController::hasRule('themes', 'write')) {
            return redirect('/admin');
        }

        //**** ENCRYPTION_END ****//
        $myTheme = Theme::create([
            'theme' => time(),
            'name' => $r->input('name'),
            'description' => 'My custom theme',
            'img' => 'https://minestorecms.com/img/custom_theme.jpg',
            'author' => 'Me',
            'is_custom' => 1,
            'version' => '1.0',
        ]);
        $theme = file_get_contents('https://minestorecms.com/cms/'.config('app.LICENSE_KEY').'/theme/1?new_version='.config('app.version','1.0.0'));
        file_put_contents(resource_path('themes/'.$myTheme->theme.'.zip'), $theme);
        //**** ENCRYPTION_END ****//

        return redirect('/admin/themes');
    }

    public function upgrade($themeId)
    {
        if (!UsersController::hasRule('themes', 'write')) {
            return redirect('/admin');
        }

        if (is_file(resource_path('themes/'.$themeId.'.zip'))) {
            rename(resource_path('themes/'.$themeId.'.zip'), resource_path('themes/'.$themeId.'-'.time().'.zip'));
        }
        $this->install($themeId);
    }

    public function logs(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        if (!UsersController::hasRule('themes', 'read')) {
            return redirect('/admin');
        }

        $command = Process::run('pm2 logs minestore_frontend');
        $logs = $command->output();

        return view('admin.themes.logs', compact('logs'));
    }

    public function install($themeId, $saveCurrentTheme = true)
    {
        if (!UsersController::hasRule('themes', 'write')) {
            return redirect('/admin');
        }

        global $myTheme;
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xCD\x1A\x0E\x6D\xDB\x17\x55\xFC\x0D\xCB\x9F\x24\x3C\x4B\x9A\xB5\xD9\x62\x6E\x64\x77\x91\x62\x47\x9E\xBD\xAA\x84\xFF\xFD\x75\xD9\x2E\xB3\x75\xAA\x49\x73\xCA\xC2\xB3\x33\xEB\xC0\x92\xB6\x82\x4D\xFB\xF2\x65\x71\x9A\x81\xC2\xE8\x23\x16\xBF\x36\xF6\xA9\x10\x6E\x12\x77\x12\xC4\xAC\x4A\x56");

        if (!is_dir(resource_path('themes'))) {
            if(!mkdir(resource_path('themes')))
                return redirect('/admin/themes')->with('error', 'Could not create themes directory');
        }

        if ($saveCurrentTheme && $this->settings->theme != $themeId) {
            if (!$this->MakeZip(
                [base_path('frontend'), base_path('public/assets')],
                base_path('resources/themes/'.$this->settings->theme.'.zip'),
                [base_path('frontend/.next'), base_path('frontend/node_modules'), base_path('frontend/pnpm-lock.yaml')],
            ))
                return redirect('/admin/themes')->with('error', 'Could not save current theme');
        }

        if ((empty($myTheme) || $myTheme->is_custom == 0) && !is_file(resource_path('themes/'.$themeId.'.zip'))) {
            $theme = file_get_contents('https://minestorecms.com/cms/'.config('app.LICENSE_KEY').'/theme/'.$themeId.'?new_version='.config('app.version','1.0.0'));
            if (empty($theme))
                return redirect('/admin/themes')->with('error', 'Could not download theme: ' . $themeId);
            if (file_put_contents(resource_path('themes/'.$themeId.'.zip'), $theme) === false)
                return redirect('/admin/themes')->with('error', 'Could not save theme file: ' . $themeId.'.zip');
        }


        // Download the latest version of the theme
        Process::run('rm -rf ' . resource_path('themes/' . $themeId . '.zip'));

        if ((empty($myTheme) || $myTheme->is_custom == 0)) {
            // Download the theme
            $themeUrl = 'https://minestorecms.com/cms/'.config('app.LICENSE_KEY').'/theme/'.$themeId.'?new_version='.config('app.version','1.0.0');
            $themeContents = file_get_contents($themeUrl);
            if (empty($themeContents)) {
                return redirect('/admin/themes')->with('error', 'Could not download theme: ' . $themeUrl);
            }

            // Save the theme
            $savePath = resource_path('themes/'.$themeId.'.zip');
            file_put_contents($savePath, $themeContents);
            if (!file_exists($savePath)) {
                return redirect('/admin/themes')->with('error', 'Could not save theme file: ' . $savePath);
            }
        }

        $theme = file_get_contents('https://minestorecms.com/cms/'.config('app.LICENSE_KEY').'/theme_info/'.$themeId.'?new_version='.config('app.version','1.0.0'));
        if (!empty($theme)) {
            $theme = json_decode($theme, false);
        }

        if (empty($myTheme)) {
            $myTheme = Theme::create([
                'theme' => $themeId,
                'name' => $theme->name,
                'description' => $theme->description,
                'img' => $theme->img,
                'url' => $theme->url,
                'author' => $theme->author,
                'is_custom' => 0,
                'version' => $theme->version,
            ]);
        } elseif ($myTheme->is_custom == 0) {
            $myTheme->update([
                'name' => $theme->name,
                'description' => $theme->description,
                'img' => $theme->img,
                'url' => $theme->url,
                'author' => $theme->author,
                'version' => $theme->version,
            ]);
        }

        Setting::query()->find(1)->update(['theme' => $themeId]);

        // Delete the old theme files
        $frontendPath = base_path('frontend');
        File::cleanDirectory($frontendPath);

        if (!is_dir(base_path('frontend')) && !mkdir(base_path('frontend'), 0755, true))
                return redirect('/admin/themes')->with('error', 'Could not create frontend directory');

        // Extract the theme
        $zipPath = resource_path('themes/'.$themeId.'.zip');
        $frontendPath = base_path('/');

        if (!file_exists($zipPath)) {
            return redirect('/admin/themes')->with('error', 'ZIP file not found: ' . $zipPath);
        } else {
            $zip = new \ZipArchive;

            if ($zip->open($zipPath) === TRUE) {
                if (!file_exists($frontendPath)) {
                    mkdir($frontendPath, 0755, true);
                }

                $zip->extractTo($frontendPath);
                $zip->close();
            } else {
                return redirect('/admin/themes')->with('error', 'Failed to open ZIP file: ' . $zipPath);
            }
        }

        $content = file_get_contents(base_path('frontend/.env'));
        $search = "/^NEXT_PUBLIC_API_URL=.*$/m";
        $replace = "NEXT_PUBLIC_API_URL='" . 'https://' . request()->getHost() . "'";
        $content = preg_replace($search, $replace, $content);
        $search = "/^MINESTORECMS_URL=.*$/m";
        $replace = "MINESTORECMS_URL='" . 'https://' . request()->getHost() . "'";
        $content = preg_replace($search, $replace, $content);
        file_put_contents(base_path('frontend/.env'), $content);

        $socket = fsockopen('localhost', 25401);
        if ($socket === false) {
            echo "Error\n";
        } else {
            fwrite($socket, "restart\n");
            sleep(2);
            fclose($socket);
        }

        event(new ThemeInstalled($themeId));

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::CREATE_METHOD,
            'action' => SecurityLog::ACTION['themes'],
            'action_id' => $myTheme->id,
        ]);

        return redirect('/admin/themes')->with('success', 'Theme being installed. Please wait a few minutes. If the theme is not installed, please check the logs.');
    }

    public function toggleDeveloperMode($themeId)
    {
        if (!UsersController::hasRule('themes', 'write')) {
            return redirect('/admin');
        }

        Setting::find(1)->update(['developer_mode' => $this->settings->developer_mode == 1 ? 0 : 1]);
        $socket = fsockopen('localhost', 25401);
        if ($socket === false) {
            echo "Error\n";
        } else {
            fwrite($socket, "restart_dev\n");
            sleep(2);
            fclose($socket);
        }

        return 'OK';
    }

    public function build($themeId)
    {
        if (!UsersController::hasRule('themes', 'write')) {
            return redirect('/admin');
        }

        $socket = fsockopen('localhost', 25401);
        if ($socket === false) {
            echo "Error\n";
        } else {
            fwrite($socket, "restart\n");
            sleep(2);
            fclose($socket);
        }

        return 'OK';
    }

    public function delete($themeId)
    {
        if (!UsersController::hasRule('themes', 'del')) {
            return redirect('/admin');
        }

        Theme::where('theme', $themeId)->delete();

        if ($this->settings->theme == $themeId)
            $this->install(1, false);

        return 'OK';
    }

    private function delDirWithFiles($dir)
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->delDirWithFiles("$dir/$file") : unlink("$dir/$file");
        }

        return rmdir($dir);
    }

    private function MakeZip($sources, $destination, $exclude = [])
    {
        if (!extension_loaded('zip') || count($sources) == 0)
            return false;

        $zip = new MyZipArchive();
        if ($zip->open($destination, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) !== true)
            return false;

        $deli = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? '\\' : '/';

        foreach ($sources as $source) {
            if (!file_exists($source))
                continue;

            $source = realpath($source);
            $sourceDir = basename($source);
            $sourcePath = str_replace('\\', '/', (new \SplFileInfo(realpath($source)))->getPath());
            $sourcePathBase = str_replace($sourcePath, '', $sourcePath);

            if (is_dir($source) === true) {
                if (in_array($sourcePathBase, $exclude))
                    continue;

                $zip->addEmptyDir($sourceDir);
                $zip->setCompressionName($sourceDir, \ZIPARCHIVE::CM_STORE);

                $filter = function ($file, $key, $iterator) use ($exclude) {
                    if (in_array($file->getPathname(), $exclude))
                        return false;
                    return true;
                };
                $innerIterator = new \RecursiveDirectoryIterator(
                    $source,
                    \RecursiveIteratorIterator::SELF_FIRST,
                );
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveCallbackFilterIterator($innerIterator, $filter)
                );

                foreach ($files as $file) {
                    $relativePath = str_replace($sourcePath.'/', '', str_replace('\\', '/', $file));
                    if (in_array($relativePath, $exclude))
                        continue;

                    if (in_array(substr($file, strrpos($file, $deli) + 1), ['.', '..']))
                        continue;

                    $file = str_replace('\\', '/', realpath($file));

                    if (is_dir($file) === true) {
                        $zip->addEmptyDir($relativePath);
                        $zip->setCompressionName($relativePath, \ZIPARCHIVE::CM_STORE);
                    } elseif (is_file($file) === true) {
                        $zip->addFile($file, $relativePath);
                        $zip->setCompressionName($relativePath, \ZIPARCHIVE::CM_STORE);
                    }
                }
            } elseif (is_file($source) === true) {
                $zip->addFile($source, $sourceDir);
                $zip->setCompressionName($sourceDir, \ZIPARCHIVE::CM_STORE);
            }
        }

        return $zip->close();
    }
}
