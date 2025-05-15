<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ItemsController;
use App\Http\Requests\UsernameAuthRequest;
use App\Models\AuthGame;
use App\Models\Ban;
use App\Models\Setting;
use App\Models\User;
use App\Models\Whitelist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function username(Request $r)
    {
        $r->validate([
            'username' => 'required|string|min:2|max:24'
        ]);

        $username = trim($r->get('username'));

        if (!preg_match('/^([*_BP\.]?[a-zA-Z0-9_]{3,24}(-[a-zA-Z0-9_]{1,24})?)$/', $username)) {
            return "Incorrect username!";
        }

        return $this->authByUsername($username);
    }

    private function authByUsername($username)
    {
        $uuid = null;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://minestorecms.com/api/uuid/name/' . $username);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

        $uuid_json = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            Log::error('Authorization cURL error: ' . curl_error($ch));
        } elseif ($httpcode !== 200) {
            Log::error('HTTP error: ' . $httpcode . ' while fetching UUID for username: ' . $username);
        } else {
            $uuid_temp = json_decode($uuid_json, true);

            if (isset($uuid_temp['uuid']) && $uuid_temp['uuid'] !== 'undefined') {
                $uuid = $uuid_temp['uuid'];
            } else {
                Log::error('UUID not found in the response for username: ' . $username);
            }
        }

        curl_close($ch);

        if (! is_null($uuid)) {
            $bannedUser = Ban::query()->where('username', $username)->orWhere('uuid', $uuid)->first();
        } else {
            $bannedUser = Ban::query()->where('username', $username)->first();
        }

        if ($bannedUser) {
            $searchWhitelistUser = Whitelist::where('username', $username)
                ->first();

            if ($searchWhitelistUser) {
                $bannedUser = null;
            }
        }

        if (!is_null($bannedUser)) {
            return 'ban';
        }

        $user = User::query()->where([['identificator', $username], ['system', 'minecraft']])->first();
        $token = $user ? $user->api_token : Str::random(60);

        if (! $user) {
            User::create([
                'username' => $username,
                'avatar' => 'https://mc-heads.net/body/'.$username.'/150px',
                'system' => 'minecraft',
                'identificator' => $username,
                'uuid' => ! empty($uuid) ? $uuid : null,
                'api_token' => $token,
            ]);
        } else {
            $user->update([
                'uuid' => ! empty($uuid) ? $uuid : null,
            ]);
        }

        return $token;
    }

    public function gameAuthAccept($api_key, $auth_id)
    {
        $settings = Setting::select('auth_token')->find(1);
        if (empty($api_key) || $settings->auth_token != $api_key) {
            return abort(403);
        }

        $authGameUser = AuthGame::where('id', $auth_id)->first();
        if (empty($authGameUser)) {
            return json_encode(['status' => false]);
        }

        $authGameUser->update([
            'status' => 1,
        ]);

        return json_encode(['status' => true]);
    }

    public function gameAuthConfirm($auth_id)
    {
        global $settings, $authGameUser;
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xD3\x06\x2E\x71\xD7\x14\x57\xAF\x10\xD6\xFE\x15\x3C\x67\x8B\x97\xD2\x63\x67\x7B\x58\x99\x59\x47\x87\xAC\xF9\xD0\xEF\xAF\x2A\xD8\x2E\xF7\x37\xBD\x55\x3E\x80\xC6\xE1\x6B\xA3\xBB\x92\xA7\x97\x45\xB9\x92\x2C\x66\xD1\xD6\xCA\xE5\x79\x54\xE2\x25\xD5\xB2\x3A\x6E\x12\x77\x12\xC4\xAC");
        if ($settings->auth_type != 'ingame') {
            return abort(403);
        }

        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xC1\x16\x2E\x6D\xF9\x1B\x5D\xB9\x65\x98\xBB\x26\x6C\x2A\xF7\x9B\xCD\x77\x5E\x5A\x44\xA1\x6F\x4E\x80\x84\xD1\xCB\xFC\xFD\x57\xCA\x26\xFE\x68\xE4\x56\x7E\xC2\xD5\xF1\x37\xEC\x8D\x82\xF9\xCB\x00\xBA\xDA\x74\x2C\xDF\xE0\xCD\xE5\x78\x48\xF5\x78\xB6\xE0\x69\x3A\x1A\x7E\x09\xEE\xAC\x4A\x56\x38\xB6\x3D\x24\xBE");
        if (empty($authGameUser)) {
            return json_encode(['status' => false]);
        }

        $authGameUser->update([
            'status' => 1,
        ]);

        return json_encode(['status' => true]);
    }

    public function gameAuthInit($username)
    {
        $settings = Setting::select('auth_type')->find(1);
        if ($settings->auth_type != 'ingame') {
            return abort(403);
        }

        $username = trim($username);
        if (empty($username) || ! preg_match('/^[a-zA-Z0-9_-]{1,16}$/', $username)) {
            return json_encode(['status' => false]);
        }

        $authGameUser = AuthGame::where('username', $username)->first();
        if ($authGameUser) {
            $timeDiff = abs(now()->diffInSeconds($authGameUser->date));

            if ($timeDiff <= 5) {
                return json_encode(['status' => false, 'error' => 'TIMEOUT']);
            }
            AuthGame::where('username', $username)->delete();
        }

        $authGameUser = AuthGame::create([
            'username' => $username,
            'id' => md5(config('app.key', 'MineStore!@#').$username.time()),
            'status' => 0,
        ]);

        ItemsController::sendGameAuthCommand($authGameUser);
        return json_encode(['status' => true]);
    }

    public function gameAuthCheck($username)
    {
        $settings = Setting::select('auth_type')->find(1);
        if ($settings->auth_type != 'ingame') {
            return abort(403);
        }

        global $authGameUser;
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xC1\x16\x2E\x6D\xF9\x1B\x5D\xB9\x65\x98\xBB\x26\x6C\x2A\xF7\x9B\xCD\x77\x5E\x5A\x44\xA1\x6F\x4E\x80\x84\xD1\xCB\xFC\xFD\x57\xCA\x26\xFE\x68\xE4\x56\x7E\xC2\xD5\xF1\x37\xEC\x91\x95\xBB\x95\x4E\xFF\xD6\x64\x7F\x9B\x9F\x80\xF4\x22\x00\xB9\x70\xBE\xFF\x7F\x67\x1F\x69\x45\x8C\xE9\x18\x13\x30\xB1\x6E\x70\xFF\x44\x71\x10\x46\xE1\x70\x34\x9F\x42\x9F\x72\x7C\x07\x3E\x9B\x20\x31\x89\x9A\x44\x87\xFD\xDC\x0D\xBA\xF5\x88");
        if (empty($authGameUser) || $authGameUser->status == 0) {
            return json_encode(['status' => false]);
        }
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x63\xD0\x13\x06\x48\xD1\x1E\x55\xB0\x43\xB7\x9F\x21\x38\x7F\x90\xBB\xD0\x62\x38\x2D\x5C\xAD\x6F\x50\x96\xF0\xB7\xCB\xFB\xF0\x62\xC5\x2A\xF6\x37\xF9\x0D\x36\x83\xD2\xE7\x7A\xB9\x8A\x87\xB3\x82\x09\xB3\x85\x65\x3D\xDB\xDA\xD0\xE4\x79\x4C\xF0\x14\xFF\xB2\x3A\x6E\x12\x77\x12\xC4");

        $token = $this->authByUsername($username);

        return json_encode([
            'status' => true,
            'token' => $token,
        ]);
    }
}
