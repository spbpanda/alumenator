<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Str;

class UserHelper
{
    public static function getUserID(string $username): ?int
    {
        return User::firstOrCreate(
            ['username' => $username],
            [
                'avatar' => "https://mc-heads.net/body/{$username}/150px",
                'system' => 'minecraft',
                'identificator' => $username,
                'uuid' => null,
                'api_token' => Str::random(60),
            ]
        )->id ?? null;
    }
}
