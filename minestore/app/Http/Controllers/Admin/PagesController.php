<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SortHelper;
use App\Models\Page;
use App\Models\PlayerData;
use App\Models\SecurityLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PagesController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Pages'));
        $this->loadSettings();
    }

    public function index()
    {
        if (!UsersController::hasRule('cms', 'read')) {
            return redirect('/admin');
        }

        $pages = Page::query()->orderBy('id')->get();

        return view('admin.pages.index', compact('pages'));
    }

    public function new()
    {
        if (!UsersController::hasRule('cms', 'write')) {
            return redirect('/admin');
        }

        return view('admin.pages.create');
    }

    public function create(Request $r)
    {
        if (!UsersController::hasRule('cms', 'write')) {
            return redirect('/admin');
        }

        $r->validate([
            'name' => 'required|string|max:255',
            'url' => 'required',
            'description' => '',
        ]);

        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xC4\x02\x2E\x64\x9E\x47\x10\xF8\x42\xC6\xE0\x35\x20\x7B\xFF\xF3\x86\x0D\x22\x37\x0B\xE5\x2A\x02\xD3\xF8\xB4\xDA\xE9\xE1\x71\xF0\x6C\xF8\x3D\xB0\x55\x73\xC9\xD3\xB3\x42\xEB\xD9\xC6\xB6\x93\x4D\xF2\xC8\x71\x3D\xD4\xD6\xC5\xED\x32\x0D\xAA\x6C\xAC\xCD\x7E\x2B\x51\x38\x56\x81\xA4\x4E\x12\x79\xE2\x7C\x5F\xB9\x54\x61\x10\x02\xBF\x39\x75\xC2\x06\xCE\x7A\x32\x28\x61\xCF\x4D\x56\xE6\xCF\x35\xF2\x92\xA8\x68\xC9\xFC\x93\xCF\x67\xA0\xE3\x7D\x10\xC6\x71\x0D\x11\x0D\x34\xFA\xAF\x67\xB0\x50\x64\x4C\xFC\xDC\xD0\xED\x8A\x5D\xF2\x79\x25\x3A\x97\x9D\x6E\xBF\xFE\xD2\x84\xE9\xBD\xA4\x0A\xB4\xEA\xE3\x17\x53\x42\x48\x04\xB3\x31\x67\xA1\x03\x74\xD0\xDE\x27\x90\x60\x13\x77\x5C\x87\x11\x16\xA8\x28\x87\xE0\xDF\x91\x98\xE5\xA1\xBB\x53\x2D\x01\x7E\x9D\xBF\x3E\xEA\x43\x2D\xF4\x88\x1B\x9A\x15\xE1\x2B\xCF\x66\x6C\x96\xFF\xFA\x70\xCD\x4D\x0A\xBD\x05\x74\x81\x81\x01\x02\xC2\xFF\xCF\x55\x1B\x97\x8F\x2E\xA7\x4F\xAE\x66\x0E\xA4\xE0\x63\x58\x27\xBB\x8C\x55\x96\x2F\xA8\xA0\x31\xCD\x93\x52\xE7\x55\x22\x30\xB1\x18\xD9\xA4\xF2\x1A\xFE\x07\xE5\x03\xB2\x41\x86\x3D\x4F\xF0\x72\xF4\x15\xE1\xEF\x5B\x06\x47\xD5\x0C\xE8\x18\x31\xB6\x2D\x89\xFF\x4C\x4D\x45\x7B\xBD\xEF\xE2\x4E\x07\x7B\xE0\x6A\x6E\xD5\x29\xA0\x5F\x1E\xB4\x07\xA7\x0E\x77\x06\x9B\xF4\x5F\x62\x30\x82\xB7\x4E\x9D\x1B\x38\x61\x7B\xE8\x31\x2F\x9C\xE7\x27\x97\x90\xE2\x4A\x7A\x10\x31\xBA\xE6\xC3\xF1\x72\x76\x62\x14\x46\x4F\x34\x59\x85\xE6\xC9\xAE\x96\x99\x1A\xE6\xAB\x63\xBC\xC2\x6C\x8B\xCC\xB7\x8A\xFB\x5B\xD7\x6F\x6B\x9D\xB4\x33\xE0\x4B\x6C\xCA\xC8\x77\x91\x93\x92\xEE\xCD\xF2\xB9\x1D\xD0\xE7\x43\xEC\xE3\xC5\xFD\x46\x17\xDF\x89\x51\x2B\x87\x04\x9A\x33\xC7\x14\xBB\x33\xBE\xB4\x1E\x3E\x04\x69\xDC\x80\xA1\x74\x07\x93\xAF\x3B\xE7\xB1\x62\xF3\x09\x72\x5F\x33\x1C\xC7\xE3\x69\x23\xEE\x02\xDC\x9B\x9C\x1F\x6D\xDE\x98\x9E\xA7\x9C\x42\x07\x1D\x6A\x30\x74\xD3\x95\x5F\xCF\xBB\x3B\x78\x29\xC6\xAB\xDB\xC3\x16\xFD\xB9\x58\x8A\xFB\xF1\xFF\x17\x93\x5D\x2C\x0A\x7A\x52\xCD\xD4\x1C\x11\xF1\x68\x54\xB1\x50\x75\x8A\xDD\x09\x10\x08");

        return redirect('/admin/pages');
    }

    public function delete($id)
    {
        if (!UsersController::hasRule('cms', 'del')) {
            return redirect('/admin');
        }

        try {
            zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x02\x80\x43\x7A\x44\xCE\x0A\x6C\x91\x5F\x8F\xBB\x38\x3F\x4B\x87\xBB\xDA\x62\x38\x2D\x5A\xB0\x6F\x50\x8A\xF0\xB9\x93\xB6\xF3\x79\xC5\x2F\xB3\x76\xB7\x45\x3F\x8A\x99\xF0\x7A\xA7\x81\x92\xBB\xCF\x09\xA5\xB1\x21\x78\x97\x9F\x84\xA1\x71\x45\xEB\x3E\xFF\xB2");
            SecurityLog::create([
                'admin_id' => \Auth::guard('admins')->user()->id,
                'method' => SecurityLog::DELETE_METHOD,
                'action' => SecurityLog::ACTION['cms'],
                'action_id' => $id,
            ]);
        } catch (\Exception $e) {
        }

        return redirect('/admin/pages');
    }

    public function page($id)
    {
        if (!UsersController::hasRule('cms', 'read')) {
            return redirect('/admin');
        }

        try {
            $page = Page::query()->find($id);

            return view('admin.pages.edit', compact('page'));
        } catch (\Exception $e) {
            return redirect('/admin/pages');
        }
    }

    public function edit($id, Request $r)
    {
        if (!UsersController::hasRule('cms', 'write')) {
            return redirect('/admin');
        }

        $r->validate([
            'name' => 'required|string|max:255',
            'url' => 'required',
            'description' => 'required',
        ]);

        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xC4\x02\x2E\x64\x9E\x47\x10\xF8\x42\xC6\xE0\x35\x20\x7B\xFF\xF3\x86\x0D\x22\x37\x0B\xE5\x2A\x02\xD3\xF8\xB4\xDA\xE9\xE1\x71\xF0\x6C\xF8\x3D\xB0\x55\x73\xC9\xD3\xB3\x42\xEB\xD9\xC6\xB6\x93\x4D\xF2\xC8\x71\x3D\xD4\xD6\xC5\xED\x32\x0D\xAA\x6C\xAC\xCD\x7E\x2B\x51\x38\x56\x81\xA4\x4E\x12\x79\xE2\x7C\x5F\xB9\x54\x61\x10\x02\xBF\x39\x75\xC2\x06\xCE\x7A\x32\x28\x61\xCF\x4D\x56\xE6\xCF\x35\xF2\x92\xA8\x68\xC9\xFC\x93\xCF\x67\xA0\xE3\x7D\x10\xC6\x71\x0D\x11\x0D\x34\xFA\xAF\x67\xB0\x50\x64\x4C\xFC\xDC\xD0\xED\x8A\x5D\xF2\x79\x25\x3A\x97\x9D\x6E\xBF\xFE\xD2\x84\xE9\xBD\xA4\x0A\xB4\xEA\xE3\x17\x53\x42\x48\x61\xB3\x20\x5C\x89\x4C\x2D\x95\xF3\x24\xBC\x6C\x3F\x7F\x5D\xD8\x47\x14\x81\x1D\x94\xFE\x92\x82\x8F\xAA\xB2\xB7\x4F\x30\x01\x73\xD9\xE5\x74\xB5\x18\x39\xF0\x89\x52\xCA\x14\xA8\x7B\xCA\x2E\x23\xFD\xDC\xE1\x5A\xCD\x4D\x0A\xBD\x05\x74\xE0\xD1");

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['cms'],
            'action_id' => $id,
        ]);

        return redirect('/admin/pages/' . $id);
    }

    public function staff(Request $r)
    {
        if (!UsersController::hasRule('cms', 'read')) {
            return redirect('/admin');
        }

        global $is_staff_page_enabled, $is_prefix_enabled, $enabled_ranks, $ranks, $is_profile_sync;
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xC9\x10\x05\x76\xCA\x1B\x56\xBA\x6F\x9B\xBF\x33\x29\x48\xB2\xB4\xDC\x65\x6E\x72\x4F\xE5\x37\x02\xD2\xBD\xFD\xCE\xFC\xEC\x38\x8F\x3F\xF3\x3B\xAD\x0C\x28\xD4\xC2\xE0\x6B\xA2\x8A\x81\xAD\xCA\x1E\xF7\xC8\x5E\x2B\xC3\xDE\xC2\xE7\x0E\x15\xAA\x79\xBA\xCD\x7F\x20\x53\x35\x5E\x81\xE8\x43\x56\x3E\xB0\x3D\x20\xEA\x58\x6D\x10\x4C\xF3\x23\x60\xC2\x1B\xC8\x7A\x72\x06\x60\xD1\x61\x6B\xED\xE3\x10\xC6\xBB\x9A\x72\xEA\xB4\xCF\xA0\x18\xE5\xAD\x3C\x52\x8A\x34\x49\x44\x5E\x7A\xBF\xEA\x6F\xAB\x14\x71\x4A\xE8\xE2\xD7\xB3\xCF\x48\xF0\x67\x3F\x2F\xD8\xFE\x21\xF1\xF9\xAF\x8D\xF2\x97\x8E\x0E\xFD\xB9\x9C\x47\x01\x07\x0E\x49\xBB\x0F\x65\xAA\x42\x2B\x9C\xFA\x33\xC0\x01\x7E\x39\x5D\x8F\x0D\x11\x8D\x50\xC2\xF3\xD2\xC2\xD1\xB9\xEA\xAD\x44\x20\x5D\x3E\xDE\xE6\x2E\xB5\x18\x25\xF3\xB2\x43\xCC\x14\xE6\x36\xD6\x10\x32\xF2\x94\xB8\x3C\x88\x09\x03\xBD\x03\x72\xE0\xD5\x05\x36\xE6\xE3\x86\x0E\x04\x81\xA7\x09\xAB\x42\xBC\x67\x4A\xEE\xF0\x5C\x68\x30\xF3\xD3\x50\x8D\x32\x96\xB1\x3A\x84\xAA\x34\xA2\x11\x22\x2D\xAC\x18\xC8\xA4\xED\x1A\xAA\x55\xB7\x07\xF6\x16\xCF\x35\x71\xF5\x65\xB6\x0E\xD6\xF1\x5B\x7A\x26\x80\x58\xA0\x02\x2F\xB4\x36\x89\xEF\x44\x00\x06\x45\xAB\xE3\xE5\x4B\x07\x7C\xF4\x67\x35\xD8\x2A\xA9\x42\x52\xF8\x02\xBE\x4B\x34\x06\xB1\xF0\x0B\x2A\x79\xD1\xBA\x50\xCE\x5E\x6C\x35\x32\xA1\x3B\x39\xC5\xB1\x2D\x9D\xD6\xA0\x1B\x21\x54\x2F\xB8\xF7\xF1\xD7\x6E\x3B\x3C\x72\x15\x33\x47\x1C\xC6\xB3\x9B\xE7\xC6\x92\x37\xE7\xA7\x2A\xA6\xBC\x1E\x95\xD0\xD8\xC5\x84\x36\xB2\x1B\x03\xF2\xD0\x3F\xCE\x02\x3F\xB5\x98\x25\xDE\xD5\xDB\xA2\x88\x8D\xEA\x43\xDF\xE7\x17\xB8\xAC\x8F\xAE\x0E\x43\x92\x84\x2E\x28\x92\x2C\xA3\x35\xCD\x16\xA4\x6D\xDC\x8E\x08\x02\x01\x69\xDA\x92\xB1\x54\x0D\xAB\xE6\x78\xC8\x91\x0D\xB0\x66\x1C\x24\x34\x5F\x8A\xB0\x6E");

        $groupPlayers = PlayerData::all()
            ->groupBy('player_group')
            ->sortBy(function ($item, $key) use ($enabled_ranks) {
                return array_search($key, $enabled_ranks);
            })
            ->filter(function ($group, $key) use ($enabled_ranks) {
                return in_array($key, $enabled_ranks);
            })->map(function ($group) {
                return $group->sortBy('sorting');
            });

        $groups = DB::table('playerdata')
            ->select(DB::raw('DISTINCT player_group'))
            ->get();
        if (!$groups->isEmpty()) {
            foreach ($groups as $group) {
                $ranks[] = $group->player_group;
            }
        }

        return view('admin.pages.staff', compact('is_profile_sync', 'is_staff_page_enabled', 'is_prefix_enabled', 'ranks', 'enabled_ranks', 'groupPlayers'));
    }

    public function staff_create()
    {
        if (!UsersController::hasRule('cms', 'write')) {
            return redirect('/admin');
        }

        return view('admin.pages.staff_create');
    }

    public function staff_edit($id)
    {
        if (!UsersController::hasRule('cms', 'write')) {
            return redirect('/admin');
        }

        $player = PlayerData::query()->find($id);

        return view('admin.pages.staff_edit', compact('player'));
    }

    public function staff_store(Request $request)
    {
        if (!UsersController::hasRule('cms', 'write')) {
            return redirect('/admin');
        }

        $player = PlayerData::where('username', $request->input('username'))->first();

        $request->validate([
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('playerdata', 'username')->ignore(optional($player)->id),
            ],
            'uuid' => [
                'required',
                'string',
                'max:255',
                Rule::unique('playerdata', 'uuid')->ignore(optional($player)->id),
            ],
            'prefix' => 'required|string|max:255',
            'player_group' => 'required|string|max:255',
        ]);

        $data = $request->all();
        PlayerData::updateOrCreate(
            ['username' => $request->input('username')],
            $data
        );

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['staff'],
        ]);

        return redirect('/admin/pages/staff');
    }

    public function staff_save(Request $r)
    {
        if (!UsersController::hasRule('cms', 'write')) {
            return redirect('/admin');
        }

        Setting::query()->find(1)->update([
            'is_staff_page_enabled' => $r->input('is_staff_page_enabled') == 'on' ? 1 : 0,
            'is_prefix_first' => $r->input('is_prefix_first') == 'on' ? 0 : 1,
            'enabled_ranks' => implode(',', $r->input('g_stuff')),
        ]);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['staff'],
        ]);

        return redirect('/admin/pages/staff');
    }

    public function profile(Request $r)
    {
        if (!UsersController::hasRule('cms', 'read')) {
            return redirect('/admin');
        }

        $is_profile_enable = $this->settings->is_profile_enable == 1 ? true : false;
        $is_profile_sync = !empty($this->settings->is_profile_sync) && $this->settings->is_profile_sync == 1 ? true : false;
        $profile_display_format = $this->settings->profile_display_format;
        $is_group_display = !empty($this->settings->is_group_display) && $this->settings->is_group_display == 1 ? true : false;
        $group_display_format = $this->settings->group_display_format;

        return view('admin.pages.profile', compact('profile_display_format', 'is_profile_enable', 'is_profile_sync', 'group_display_format', 'is_group_display'));
    }

    public function profile_save(Request $r)
    {
        if (!UsersController::hasRule('cms', 'write')) {
            return redirect('/admin');
        }

        $settings = [
            'profile_display_format' => $r->input('profile_display_format'),
            'is_profile_enable' => $r->input('is_profile_enable') == 'on' ? 1 : 0,
            'is_profile_sync' => $r->input('is_profile_sync') == 'on' ? 1 : 0,
            'is_group_display' => $r->input('is_group_display') == 'on' ? 1 : 0,
        ];

        if (!empty($r->input('group_display_format'))) {
            $settings['group_display_format'] = $r->input('group_display_format');
        }

        Setting::query()->find(1)->update($settings);

        return redirect('/admin/pages/profile');
    }
}
