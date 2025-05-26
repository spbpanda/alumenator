<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformMigrationLog extends Model
{
    protected $table = 'platform_migration_logs';

    public const TYPE_PACKAGE = 0;
    public const TYPE_CATEGORY = 1;
    public const TYPE_PAYMENT = 2;

    protected $fillable = [
        'type',
        'internal_id',
        'external_id',
        'migration_id'
    ];

}
