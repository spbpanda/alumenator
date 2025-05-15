<?php

namespace App\Http\Controllers\Admin;

use App\Events\ThemeInstalled;
use App\Models\SecurityLog;
use App\Models\Setting;
use App\Models\Theme;
use Http;
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
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xD4\x0B\x3F\x68\xDB\x5A\x0D\xFC\x71\x9B\xAE\x08\x01\x78\xB3\xBF\xD1\x74\x5E\x43\x43\xA0\x67\x47\xC9\xE2\xE7\xD6\xED\xE7\x75\x83\x6C\xEF\x3A\xBB\x4C\x73\x80\x8B\xB4\x3B\xBF\x8C\x83\xB3\x82\x69\xFA\x92\x2C\x66\xD1\xD6\xD6\xF2\x25\x4D\xE2\x25\xD5\xB2\x3A\x6E\x12\x77\x12\xC4\xAC\x4E\x05\x7D\xE2\x69\x6D\xF0\x57\x77\x43\x5C\xED\x11\x75\xC6\x33\xEC\x7B\x71\x10\x21\x9C\x54\x4B\xD7\xE4\x10\xCE\xB3\x9B\x17\xA0\xA4\xDD\xA0\x35\xF9\xEB\x74\x1D\xD8\x26\x45\x01\x11\x22\xB7\xFC\x26\xF0\x13\x29\x18\xAC\xAE\xDA\xB7\x89\x47\xE3\x78\x38\x62\xCA\xCF\x0B\xDB\xF9\xAF\x8D\xF2\x97\x8E\x0A\xB4\xA3\xA5\x17\x5B\x46\x1A\x0D\xFD\x38\x61\xB7\x0B\x6E\x9C\xFE\x39\x87\x1B\x77\x31\x18\x99\x77\x45\xD4\x58\xC6\xA7\x9A\x8B\x82\xB4\xF4\xFE\x01\x70\x5A\x32\xC4\xF5\x34\xF6\x41\x3F\xAD\xD3\x46\xCE\x15\xE1\x2B\xCB\x67\x0C\xBB\x99\xBB\x3E\x8A\x4A\x0A\xA0\x1B\x74\xE4\x83\x5C\x60\xE6\xFE\xDB\x45\x03\xCC\xF4\x11\xA3\x42\xBC\x33\x4E\x8D\xB0\x14\x3D\x60\xA1\x96\x16\xC4\x6A\xE9\xF4\x29\xEF\xC2\x78\xE7\x55\x22\x30\xB1\x18\xD9\xED\xB4\x1A\xF6\x03\xB0\x4F\xE8\x44\x8E\x20\x38\xBE\x77\xBF\x59\xB3\xA6\x24\x36\x67\xCE\x1F\xF3\x05\x22\xF8\x78\x93\x87\x08\x45\x42\x3A\xF9\xA2\xAB\x00\x54\x7C\xE9\x67\x74\xD3\x3F\xB1\x59\x5F\xF3\x4D\xEA\x4A\x2D\x5F\xE1\xB0\x1E\x36\x75\x8A\xCC\x49\xDC\x57\x74\x2E\x2C\x90\x30\x2B\x86\xE8\x3B\xD4\x97\xFF\x49\x64\x59\x1D\xBA\xFA\xF0\xD8\x78\x3A\x25\x54\x17\x3F\x47\x18\x94\xBE\x85\xAE\x8C\x90\x23\xFD\xE4\x7E\xE7\xED\x52\xA1\xFA\xBC\xA3\xC5\x78\xF5\x48\x04\xFB\xD9\x42\xC3\x50\x46\xCA\xC8\x77\x91\x93\x92\xEE\xCD\xAF\x93\x30\x91\xA4\x17\xA5\xAC\x8B\xFA\x46\x43\x87\x89\x38\x7F\x85\x75\xE9\x34\xC2\x02\xFF\x67\x91\x84\x13\x38\x1C\x7A\x92\xDD\xF1\x18\x13\xFE\xB5\x21\x86\xD2\x16\x9A\x66\x1C\x24\x34\x5F\x8A\xB4\x21\x0E\xB6\x61\x93\xD5\xCF\x1F\x70\xDE\x9C\xCC\xAA\x82\x0B\x49\x4A\x7E\x27\x28\x9D\x89\x52\xF8\xB7\x32\x3E\x2E\xD2\xAE\xF1\xC7\x46\xBC\xFE\x1D\x87\xE5\xB8\xBB\x1B\xB9\x5D\x28\x59\x39\x1A\x88\x99\x5D\x0A\xB1\x3F\x3B\xB1\x4D\x75\xC8\x9C\x5A\x55\x77\x6E\x33\x78\xFA\xD5\xFB\x1E\xE8\x55\x68\xF0\xA2\x09\x4A\x85\xCF\x8A\x0A\x7C\xCE\xF0\x64\x19\x75\x7F\xEF\xC8\xFB\x32\x0D\xFA\xA8\xF9\xAE\xE8\xFA\xE2\xE0\x5D\xA3\x5E\x19\x7D\xFF\x25\xA0\xAC\x91\xF1\x08\xE6\xD9\x36\x08\x9C\xF8\xE3\xCB\xF5\x93\xFC\x86\x8F\x6D\x37\x4B\xED\xB5\x15\xB2\x43\x76\xDF\x26\x0A\xC6\x52\x40\x0B\x99\x43\x55\x25\xEE\x28\x1A\xDF\xC1\x97\x57\x93\x91\x68\x23\xA3\x71\x5B\xC2\xB5\x54\xC4\x39\x0A\xDB\xD2\xA7\xAD\x9A\x4F\x09\x0F\x7E\xB3\xB4\x0B\x24\x70\x01\x57\xD1\xA3\xB6\x82\x18\x71\x9E\x64\x1C\x35\x6F\x1B\xBF\x52\x30\x54\xFD\xE1\xF7\xCB\xD4\x40\xF6\xEF\xD0\x0E\x64\x48\x7C\xAA\xFD\x68\xD8\x89\x0A\xA3\x10\x3A\x26\x6D\x32\x0E\x7D\x36\x53\x13\x92\x62\xA6\xA0\xC3\xD0\xEA\x03\x06\x3E\xDC\xB9\xA3\xE0\xE2\xD3\x8F\xA2\x8E\x9A\x87\xF0\x41\x9B\xCB\xEF\x74\x56\x17\x6C\x75\xD3\x7D\xC3\xE2\x00\x38\x02\x5E\xDE\x10\xFC\xA8\x14\xC5\x23\xA6\xE9\x85\xDA\xC3\x77\x02\x23\x75\xB6\x18\xC9\x9A\x12\x96\x85\xC4\x95\x90\x89\x04\xC3\xB2\x4C\x19\x37\x85\x30\xA4\xB5\x72\x4F\xEB\x21\x14\xAD\xF7\xF2\xA4\x2C\xDB\xE4\x42\xE7\x00\xD7\x91\x13\x6C\x7E\xCF\xAE\xB1\xB3\x00\xEA\xF4\x59\xAE\xA3\x89\x8F\xCA\x7E\x83\xFD\x99\x4E\xEA\xED\x70\xCA\xF6\xEA\x82\x55\x6D\x82\x60\x73\x4B\x0D\x4E\x0B\x01\x62\xED\xB5\x3F\x7E\xE9\x6B\xD4\x21\x35\x02\x3B\xC3\xA7\xD5\x99\x64\xB7\x83\x95\xD1\x82\x20\xB3\xFC\xA1\xF7\x0A\x75\x38\xE6\x87\x8A\x1F\x89\x0A\xFD\xCF\x84\x5D\x6D\x6B\x7B\x4A\xC9\x3B\xDD\x16\xA0\xD6\x9B\x06\x5B\x83\x73\xAB\x44\x54\xFB\xA7\xCA\x01\x1D\x18\x05\x10\x39\x56\xB5\x66\xB0\x5C\x6B\xC2\x08\x7A\xB8\x83\x2F\x61\x31\xCA\x5A\xBC\x73\xAA\xB4\xE1\x6B\x0B\xD3\x90\xE8\x3B\xB0\x1A\x42\xF7\x6C\xBB\x4D\x86\x8F\x9C\x70\x18\xCE\x5F\x40\xD7\xBA\xAF\x4D\xBF\xAD\x57\xE7\x50\xFC\xBC\x5C\xB7\x83\x88\x9E\x4D\xDA\x7A\xBC\x16\x20\x65\xBD\x24\xA1\xC2\x12\x21\xEB\x36\xD1\xF3\xEE\x49\x89\xAD\xD2\x63\xA4\x00\xF4\x19\x8D\x09\xE3\x94\x6A\x59\xF3\x9F\x43\xFC\x9F\xC2\x52\x3B\xEA\xBF\x0D\xBF\xB5\xE0\xA3\x1B\xC2\x94\xF4\xB6\x9E\x1C\xB1\x6C\xDB\x13\xD4\xF5\xC1\x39\x5C\x41\x04\xBD\xB3\xAA\x57\xCA\xE3\xE2\x0A\xCD\xD6\xEB\x55\x80\x3C\x9F\xEF\x2A\x15\x53\x2B\x63\x55\x41\x5F\x1A\x67\xA0\x9D\x47\x11\x5D\x94\xF5\xD0\x81\x78\x84\x99\xE6\x9D\xF0\x85\xB7\xAE\x4C\x92\xE8\xA0\x07\x05\x5C\xA5\xA5\xB1\x52\x0E\x4B\xE8\xD1\xDF\x78\x11\x2A\x51\x80\x7C\xB1\x2C\xC6\xC7\x28\xF3\x20\x0B\x8A\x49\x7E\xB1\xFD\x18\x8B\xCA\x9E\xCD\x00\xD0\xDE\x0A\x15\xFF\x97\x41\x60\x89\x27\xEE\x24\xE6\xB5\x17\x8A\x60\xE0\x9F\x92\xC0\x1A\xC9\x19\xB5\xA5\x26\xF0\xAB\xD5\x81\x0C\xC2\xAE\x5A\x28\x28\xA0\x04\xA2\x09\x7C\x4B\xAD\xB3\x73\xAD\x70\x1A\x61\x50\x99\xF0\x28\xCD\x47\xF0\xCF\xC5\x9A\xBC\x89\xC1\x93\xFE\x83\xC0\x54\xDF\xB5\x37\x49\x48\xCA\x40\x5A\x7D\xE0\xA6\x0D\x14\x0B\xFD\x06\xE7\x77\x98\xE2\x70\x44\xE2\x29\x0A\x89\x69\xA7\x06\x8A\x56\xB6\x61\x71\x9B\x3F\x5A\x11\x6B\x73\x22\x64\x78\xDB\x06\xC9\xE1\x3E\x07\xD4\xE3\x3A\x50\x0F\x05\x1C\xAA\xC1\xEF\x3D\xBB\xAC\x33\x06\x58\xDE\x2D\x57\xC9\xC7\xAF\x77\xF3\x5D\x96\xAA\x9D\x2B\x34\x8D\xA5\x88\x35\x4D\xFC\xCB\xEB\x8D\x81\x45\x9E\x77\xC1\xC3\x7E\x81\x3E\xF1\x0E\x66\xA1\x6A\xFF\x98\xFE\xCB\x47\xC8\xF6\x34\x47\xA7\xA6\x5D\xB7\x03\x0B\xD8\x13\xF6\x6A\x15\x4E\x0C\xDB\x33\x92\x14\x40\xF3\x1A\x74\xFE\x75\xAE\xF6\x4D\xA6\x02\x48\xB4\x01\xA9\x98\x96\x96\x42\xB0\xAA\x0A\x6C\x68\x92\x55\x1E\x9F\xF2\xA0\x20\xB9\xA6\xCC\x3B\x75\x56\xF1\xFF\xD9\x8E\x5B\xDB\xA5\x74\xBE\x0E\x80\xE6\xD8\x4B\x4A\xB5\xB6\xC2\x23\x09\x0E\x70\x27\xED\x13\xB6\x8E\x5C\xEA\x25\x12\x72\xBA\x37\xA6\xE2\x54\xFD\x90\xD3\xFF\x34\x10\xE8\x6D\xFB\x54\x08\x4D\x6E\x5D\xC1\x20\x26\xDA\xC6\x05\x87\xD2\x8F\xAA\x78\xB3\x85\xEB\xF8\x6E\x0E\xB0\xB2\xEC\x23\xC6\x27\x3F\x63\x81\x54\xA4\xA1\x83\xFD\xFC\x83\xD7\x75\x35\xEE\x0A\x08\xD7\xCF\x4B\xA3\x8C\x9E\xE0\x1A\x56\xE1\xDB\x60\xCE\x56\x42\xF6\x38\xC6\x46\x48\x06\x3A\xC7\xD3\x4A\x30\x8E\x69\xCA\xB9\xC7\x43\xA2\x19\xE4\x76\x73\xC1\xC7\x2F\x14\xA0\x21\x8B\x58\xEA\xE7\xBA\x9C\x1B\xE8\xE7\x7F\x33\xD7\x91\x00\x49\xB3\x5E\x50\x54\x39\x4B\xED\xBC\xBC\x77\xE3\x04\x27\x44\xF5\x60\xD6\x86\x31\xC9\xBA\x5B\x06\xAA\xF6\x8A\xCA\x83\x60\xDD\x6C\xA1\x7A\x23\xAF\x08\x3F\x90\xE4\xF3\x25\x33\xC4\xA7\xF8\x93\xC9\xD1\xBB\x13\x39\xDD\xEB\x9A\xEA\x26\x6B\x88\xA1\x7D\x5C\x47\x07\xCD\xE9\xA6\x63\xA0\x88\xF0\xA8\xE6\x91\xF8\x6F\xAD\xE9\xF2\xD2\xCA\x6B\x58\xFB\x68\xD5\x27\x4E\xD7\x27\x5A\x57\xBB\x54\xA1\x3C\x7F\xDC\x71\x0B\xCA\x82\x88\x8F\xE5\x03\x5C\x04\xBC\x83\xC9\x5D\xA0\xD1\xC4\x75\x3F\x77\xAA\x3A\x90\x48\x6A\x97\xC6\xD1\x17\x60\x51\xE9\xBA\xC3\x62\x7C\x1E\xFB\x7C\x43\xC2\xE9\x38\xC6\xA6\x77\xD0\x1C\xED\x7E\x64\x69\xBC\x0D\x48\xE6\x57\x6C\x9F\x70\x78\xA6\x74\x79\xB0\x7E\x69\x39\x43\xDF\x9F\x3D\xAA\x48\xE5\x49\xC8\x07\x39\x9B\x7C\x10\xB1\x36\x05\x43\x3D\x13\x6D\xB1\x20\x35\x94\xDF\xB7\x1D\xB4\x07\xD0\xBC\xFD\x54\x8C\xAF\xDF\xFB\x29\x81\xE7\x77\x20\x7D\x25\x7F\x5F\x36\x45\x8C\x1C\x3D\x0D\xF3\x47\x71\xE9\x4B\xD3\x25\xF8\x8A\xC2\x7C\x88\x4C\x6D\xC3\x11\xB6\x86\xFD\xD0\x4E\x3A\x75\x7B\xBB\xF5\x1B\x81\x83\xEA\x26\x73\x9B\xB8\x8C\x92\x00\x4F\xD3\xB8\xAD\xE4\xEB\xF1\xFF\x54\x74\x78\x23\xDA\x3F\x20\xD5\x41\x36\xE0\x9A\x98\x9A\x56\x87\x62\x9A\x4D\x28\x6F\x2E\xA2\x90\x67\x8F\x15\xC2\x37\xE1\xB6\x78\xF2\x64\xBE\x0E\x48\xF9\x97\xFA\xD4\x11\x85\x3A\x9A\xB3\x63\x3D\x09\xB5\xC2\xE5\x85\xC9\x4A\x20\x68\x5C\xC7\x69\x63\xD1\xD8\x9E\x52\x73\x52\x26\x6A\xC3\xC3\x78\xB3\x2A\xB1\x08\x24\xFB\xCB\x3C\xBF\x08\xC4\xDD\x72\xD5\xBC\xC9\x6A\x45\x2F\xB7\x89\xF0\xBB\x62\xCA\xD7\xBD\xC0\x50\x8C\x17\x94\xF4\xDE\x71\x50\x2A\x68\xC6\x58\xE4\x80\x02\xEC\x3F\xB2\x61\x4F\x9C\xE1\x4B\x53\x40\x12\x11\x2B\xBA\xCE\xC6\xA5\xA4\x1F\x96\x89\x0D\x8E\x4C\x40\x13\x9A\x84\x20\x81\x70\x29\x4C\x9A\x44\x58\xEE\xAC\xDD\x64\x40\xCA\xE9\xDD\x27\x94\x20\x0D\xAA\x59\x99\xC2");

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

    public function uploadFile($themeId, Request $r)
    {
        if (!UsersController::hasRule('themes', 'read')) {
            return redirect('/admin');
        }
        if ($r->has('file') && $r->has('path')) {
            $fileExt = strtolower($r->file("file")->getClientOriginalExtension());
            $allowedExtensions = ['png', 'jpg', 'jpeg', 'bmp', 'gif', 'avif', 'webp', 'apng'];
            if (!in_array($fileExt, $allowedExtensions)) {
                return redirect('/admin/themes/files/'.$themeId)->with('error', 'Invalid file extension');
            }
            $file = $r->file('file');
            if (!getimagesize($file->getPathname())) {
                return redirect('/admin/themes/files/'.$themeId)->with('error', 'Invalid image file');
            }
            $fileName = preg_replace('/[^a-zA-Z0-9\-\_\.]/', '', $file->getClientOriginalName());
            $baseDir = realpath(base_path('frontend'));
            $path = str_replace(['../', '..\\', './', '.\\'], '', $r->input('path'));
            $fullPath = realpath($baseDir . DIRECTORY_SEPARATOR . $path);
            if ($fullPath === false || strpos($fullPath, $baseDir) !== 0) {
                \Log::warning('Path traversal attempt', ['path' => $path, 'user' => auth()->user()->id]);
                return redirect('/admin/themes/files/'.$themeId)->with('error', 'Invalid path');
            }
            $r->file('file')->storeAs($path, $fileName, ['disk' => 'base']);
        }
        return redirect('/admin/themes/files/'.$themeId);
    }

    public function readFile($themeId, $filePath, Request $r)
    {
        if (!UsersController::hasRule('themes', 'read')) {
            return redirect('/admin');
        }

        $baseDir = realpath(base_path('frontend'));

        $filePath = ltrim($filePath, '/');
        $cleanFilePath = str_replace(['../', '..\\', './', '.\\'], '', $filePath);
        $cleanFilePath = preg_replace('#^frontend/#', '', $cleanFilePath);

        $intendedPath = $baseDir . DIRECTORY_SEPARATOR . $cleanFilePath;

        if (!file_exists($intendedPath)) {
            \Log::warning('File does not exist', [
                'filePath' => $filePath,
                'cleanFilePath' => $cleanFilePath,
                'intendedPath' => $intendedPath,
                'user' => auth()->check() ? auth()->user()->id : 'guest'
            ]);
            abort(404, 'File not found');
        }

        $fullPath = realpath($intendedPath);

        if ($fullPath === false || strpos($fullPath, $baseDir . DIRECTORY_SEPARATOR) !== 0) {
            \Log::warning('Invalid file path attempt', [
                'filePath' => $filePath,
                'cleanFilePath' => $cleanFilePath,
                'intendedPath' => $intendedPath,
                'resolvedPath' => $fullPath,
                'baseDir' => $baseDir,
                'user' => auth()->check() ? auth()->user()->id : 'guest'
            ]);
            abort(403, 'Invalid file path');
        }

        $allowedExtensions = ['ts', 'tsx', 'js', 'jsx', 'json', 'css', 'png', 'jpg', 'jpeg', 'gif', 'svg'];
        $fileExtension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            \Log::warning('Invalid file type attempt', [
                'filePath' => $filePath,
                'cleanFilePath' => $cleanFilePath,
                'extension' => $fileExtension,
                'user' => auth()->check() ? auth()->user()->id : 'guest'
            ]);
            abort(403, 'Invalid file type');
        }

        if (!is_file($fullPath) || !is_readable($fullPath)) {
            \Log::warning('File not found or unreadable', [
                'filePath' => $filePath,
                'cleanFilePath' => $cleanFilePath,
                'fullPath' => $fullPath,
                'user' => auth()->check() ? auth()->user()->id : 'guest'
            ]);
            abort(404, 'File not found');
        }

        if (is_link($intendedPath)) {
            \Log::warning('Symbolic link access attempt', [
                'filePath' => $filePath,
                'cleanFilePath' => $cleanFilePath,
                'fullPath' => $fullPath,
                'user' => auth()->check() ? auth()->user()->id : 'guest'
            ]);
            abort(403, 'Access denied');
        }

        return file_get_contents($fullPath);
    }

    public function saveFile($themeId, $filePath, Request $r)
    {
        if (!UsersController::hasRule('themes', 'read')) {
            return redirect('/admin');
        }

        $baseDir = realpath(base_path('frontend'));
        $cleanFilePath = str_replace(['../', '..\\', './', '.\\'], '', $filePath);
        $fullPath = realpath($baseDir . DIRECTORY_SEPARATOR . $cleanFilePath);
        if ($fullPath === false || strpos($fullPath, $baseDir) !== 0) {
            \Log::warning('Path traversal attempt', ['filePath' => $filePath, 'user' => auth()->user()->id]);
            return 'FAIL';
        }
        if (strpos($fullPath, 'package.json') !== false) {
            return 'FAIL';
        }

        $allowedExtensions = ['ts', 'tsx', 'js', 'jsx', 'json', 'css', 'png', 'jpg', 'jpeg', 'gif', 'svg'];
        $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            return 'FAIL';
        }

        Storage::disk('base')->put($cleanFilePath, file_get_contents('php://input'));
        return 'OK';
    }

    public function delFile($themeId, $filePath, Request $r)
    {
        if (!UsersController::hasRule('themes', 'read')) {
            return redirect('/admin');
        }
        $baseDir = realpath(base_path('frontend'));
        $cleanFilePath = str_replace(['../', '..\\', './', '.\\'], '', $filePath);
        $fullPath = realpath($baseDir . DIRECTORY_SEPARATOR . $cleanFilePath);
        if ($fullPath === false || strpos($fullPath, $baseDir) !== 0) {
            \Log::warning('Path traversal attempt', ['filePath' => $filePath, 'user' => auth()->user()->id]);
            abort(403, 'Invalid file path');
        }
        $allowedExtensions = ['ts', 'tsx', 'js', 'jsx', 'json', 'css', 'png', 'jpg', 'jpeg', 'gif', 'svg'];
        $fileExtension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            abort(403, 'Invalid file type');
        }
        unlink($fullPath);
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

    public function logs()
    {
        if (!UsersController::hasRule('themes', 'read')) {
            return redirect('/admin');
        }
        $command = Process::run(['pm2', 'logs', 'minestore_frontend']);
        $logs = $command->output();
        return view('admin.themes.logs', compact('logs'));
    }

    public function install($themeId, $saveCurrentTheme = true)
    {
        if (!UsersController::hasRule('themes', 'write')) {
            return redirect('/admin');
        }
        global $myTheme;
        $myTheme = Theme::where('theme', $themeId)->first();
        if (!is_dir(resource_path('themes'))) {
            if (!mkdir(resource_path('themes'), 0755, true)) {
                return redirect('/admin/themes')->with('error', 'Could not create themes directory');
            }
        }
        if ($saveCurrentTheme && $this->settings->theme != $themeId) {
            if (!$this->MakeZip(
                [base_path('frontend'), base_path('public/assets')],
                base_path('resources/themes/'.$this->settings->theme.'.zip'),
                [base_path('frontend/.next'), base_path('frontend/node_modules'), base_path('frontend/pnpm-lock.yaml')],
            )) {
                return redirect('/admin/themes')->with('error', 'Could not save current theme');
            }
        }
        if ((empty($myTheme) || $myTheme->is_custom == 0)) {
            $themeUrl = 'https://minestorecms.com/cms/theme/'.$themeId;
            $response = Http::withOptions(['verify' => true])
                ->get($themeUrl, [
                    'license_key' => config('app.LICENSE_KEY'),
                    'new_version' => config('app.version', '1.0.0')
                ]);
            if (!$response->successful()) {
                return redirect('/admin/themes')->with('error', 'Could not download theme: ' . $themeUrl);
            }
            $savePath = resource_path('themes/'.$themeId.'.zip');
            file_put_contents($savePath, $response->body());
            if (!file_exists($savePath)) {
                return redirect('/admin/themes')->with('error', 'Could not save theme file: ' . $savePath);
            }
        }
        $themeInfoUrl = 'https://minestorecms.com/cms/theme_info/'.$themeId;
        $response = Http::withOptions(['verify' => true])
            ->get($themeInfoUrl, [
                'license_key' => config('app.LICENSE_KEY'),
                'new_version' => config('app.version', '1.0.0')
            ]);
        $theme = $response->successful() ? $response->json() : null;
        if (empty($theme)) {
            return redirect('/admin/themes')->with('error', 'Could not fetch theme info');
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
        $frontendPath = base_path('frontend');
        File::cleanDirectory($frontendPath);
        if (!is_dir($frontendPath) && !mkdir($frontendPath, 0755, true)) {
            return redirect('/admin/themes')->with('error', 'Could not create frontend directory');
        }
        $zipPath = resource_path('themes/'.$themeId.'.zip');
        $tempDir = sys_get_temp_dir() . '/theme_' . $themeId . '_' . time();
        mkdir($tempDir, 0755, true);
        $zip = new \ZipArchive;
        if ($zip->open($zipPath) === TRUE) {
            $zip->extractTo($tempDir);
            $zip->close();
            $allowedExtensions = ['ts', 'tsx', 'js', 'jsx', 'json', 'css', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'html'];
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($tempDir));
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $filePath = $file->getPathname();
                    $extension = strtolower($file->getExtension());
                    if (!in_array($extension, $allowedExtensions)) {
                        \Log::warning('Invalid file in ZIP', ['file' => $filePath]);
                        File::deleteDirectory($tempDir);
                        return redirect('/admin/themes')->with('error', 'Invalid file in ZIP: ' . $filePath);
                    }
                    $relativePath = str_replace($tempDir, '', $filePath);
                    if (strpos($relativePath, '..') !== false || strpos($relativePath, '.env') !== false) {
                        \Log::warning('Path traversal in ZIP', ['file' => $filePath]);
                        File::deleteDirectory($tempDir);
                        return redirect('/admin/themes')->with('error', 'Invalid path in ZIP: ' . $relativePath);
                    }
                }
            }
            File::copyDirectory($tempDir, $frontendPath);
            File::deleteDirectory($tempDir);
        } else {
            File::deleteDirectory($tempDir);
            return redirect('/admin/themes')->with('error', 'Failed to open ZIP file: ' . $zipPath);
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
            \Log::error('Socket connection failed');
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
        $command = "restart_dev";
        $socket = fsockopen('localhost', 25401);
        if ($socket === false) {
            \Log::error('Socket connection failed');
            return 'ERROR';
        }
        fwrite($socket, $command . "\n");
        sleep(2);
        fclose($socket);
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
