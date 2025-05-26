<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformMigration extends Model
{
    protected $table = 'platform_migrations';

    public const STATUS_CREATED = 0;
    public const STATUS_PENDING = 1;
    public const STATUS_COMPLETED = 2;
    public const STATUS_FAILED = 3;

    protected $fillable = [
        'platform_name',
        'platform_token',
        'platform_key',
        'status'
    ];
}
