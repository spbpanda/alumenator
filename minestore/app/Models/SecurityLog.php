<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * SecurityLog
 *
 * @property int $admin_id
 * @property int $method
 * @property int $action
 * @property int $action_id
 * @property string $extra
 * @property \Illuminate\Support\Carbon|null $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|SecurityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SecurityLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SecurityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|SecurityLog whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SecurityLog whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SecurityLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SecurityLog whereActionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SecurityLog whereExtra($value)
 * @mixin Eloquent
 */
class SecurityLog extends Model
{
    const CREATE_METHOD = 0;
    const UPDATE_METHOD = 1;
    const DELETE_METHOD = 2;

    const ACTION = [
        'unknown' => 0,
        'packages' => 1, // +
        'variables' => 2, // +
        'settings' => 3, // +
        'coupons' => 4, // +
        'cms' => 5, // +
        'referers' => 6, // +
        'payments' => 7, // +
        'subs' => 8,
        'fraud' => 9, // +
        'taxes' => 10, // +
        'announcement' => 11, // +
        'bans' => 12, // +
        'lookup' => 13,
        'global_commands' => 14, // +
        'teams' => 15, // +
        'statistics' => 16,
        'api' => 17, // +
        'themes' => 18, // +
        'console' => 19,
        'whitelist' => 20, // +
        'email' => 22, // +
        'discord' => 23, // +
        'links' => 24, // +
        'homepage' => 25, // +
        'socials' => 26, // +
        'featured_packages' => 27, // +
        'currency' => 28, // +
        'donation_goals' => 29, // +
        'remove_all_payments' => 30, // +
        'remove_all_users' => 31, // +
        'reset_banlist' => 32, // +
        'wipe_playerdata' => 33, // +
        'full_wipe' => 34, // +
        'authorization_type' => 35, // +
        'sales' => 36, // +
        'promoted_packages' => 37, // +
        'staff' => 38, // +
        'login' => 39, // +
        'ip_checks' => 40, // +
        'upgrade' => 41, // +
        'giftcards' => 42, // +
        'download_pdf' => 43, // +
        'spending_limit' => 44, // +
        'category' => 45, // +
        'p_add_note' => 46, // +
        'p_force_delivery' => 47, // +
        'servers' => 48, // +
        'merchant' => 49, // +
        'install' => 50, // +
        'donation_goal' => 51, // +
    ];


    protected $table = 'security_logs';
    public $timestamps = true;

    protected $fillable = [
        'id',
        'admin_id',
        'method',
        'action',
        'action_id',
        'extra',
    ];

}
