<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Setting
 *
 * @property int $id
 * @property string $site_name
 * @property string $site_desc
 * @property string $serverIP
 * @property string $serverPort
 * @property string $withdraw_type
 * @property string $withdraw_game
 * @property string $webhook_url
 * @property string $discord_guild_id
 * @property string $discord_url
 * @property string $discord_bot_token
 * @property int $discord_bot_enabled
 * @property string $discord_client_id
 * @property string $discord_client_secret
 * @property string $index_content
 * @property string $index_deal
 * @property string $lang
 * @property string $allow_langs
 * @property string $currency
 * @property string $allow_currs
 * @property int $is_virtual_currency
 * @property string $virtual_currency
 * @property string $virtual_currency_cmd
 * @property string $block_1
 * @property string $block_2
 * @property string $block_3
 * @property string $facebook_link
 * @property string $instagram_link
 * @property string $discord_link
 * @property string $twitter_link
 * @property string $steam_link
 * @property string $tiktok_link
 * @property string $youtube_link
 * @property string $auth_type
 * @property int $is_staff_page_enabled
 * @property int $is_prefix_enabled
 * @property string $enabled_ranks
 * @property int $is_profile_enable
 * @property string $profile_display_format
 * @property int $is_profile_sync
 * @property string $group_display_format
 * @property int $is_group_display
 * @property int $is_ref
 * @property int $details
 * @property int $cb_threshold
 * @property int $cb_period
 * @property int $cb_username
 * @property int $cb_ip
 * @property int $cb_bypass
 * @property int $cb_local
 * @property int $cb_limit
 * @property int $cb_limit_period
 * @property int $cb_geoip
 * @property string $cb_countries
 * @property int $is_api
 * @property string $api_secret
 * @property int $smtp_enable
 * @property string $smtp_host
 * @property string $smtp_port
 * @property int $smtp_ssl
 * @property string $smtp_user
 * @property string $smtp_pass
 * @property string $smtp_from
 * @property int $enable_globalcmd
 * @property int $is_featured
 * @property string $featured_items
 * @property int $is_featured_offer
 * @property int $theme
 * @property int $is_maintenance
 * @property string $maintenance_ips
 * @property int $is_sale_email_notify
 * @property int $developer_mode
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereAllowCurrs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereAllowLangs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereApiSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereAuthType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereBlock1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereBlock2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereBlock3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCbBypass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCbCountries($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCbGeoip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCbIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCbLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCbLimitPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCbLocal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCbPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCbThreshold($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCbUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereDiscordGuildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereDiscordLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereDiscordUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereEnableGlobalcmd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereEnabledRanks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereFacebookLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereFeaturedItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereGroupDisplayFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereIndexContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereIndexDeal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereInstagramLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereIsApi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereIsFeaturedOffer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereIsGroupDisplay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereIsMaintenance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereIsPrefixEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereIsProfileEnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereIsProfileSync($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereIsRef($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereIsSaleEmailNotify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereIsStaffPageEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereIsVirtualCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereLang($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereMaintenanceIps($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereProfileDisplayFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereServerIP($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereServerPort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSiteDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSiteName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSmtpEnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSmtpHost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSmtpPass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSmtpPort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSmtpSsl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSmtpUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSteamLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereTheme($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereTiktokLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereTwitterLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereVirtualCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereVirtualCurrencyCmd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereWebhookUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereWithdrawGame($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereWithdrawType($value)
 * @mixin \Eloquent
 */
class Setting extends Model
{
    protected $fillable = [
        'site_name', 'site_desc', 'serverIP', 'serverPort',
        'withdraw_type', 'withdraw_game', 'webhook_url', 'auth_type',
        'index_deal', 'index_content',
        'lang', 'allow_langs', 'currency', 'allow_currs', 'is_virtual_currency', 'virtual_currency', 'virtual_currency_cmd',
        'discord_guild_id', 'discord_url', 'discord_bot_token', 'discord_bot_enabled', 'discord_client_id', 'discord_client_secret',
        'block_1', 'block_2', 'block_3',
        'facebook_link', 'instagram_link', 'discord_link', 'twitter_link', 'steam_link', 'tiktok_link', 'youtube_link',
        'is_staff_page_enabled', 'is_prefix_enabled', 'enabled_ranks',
        'is_profile_enable', 'profile_display_format', 'is_profile_sync',
        'group_display_format', 'is_group_display',
        'is_ref', 'details',
        'cb_threshold', 'cb_period', 'cb_username', 'cb_ip', 'cb_bypass', 'cb_local', 'cb_limit', 'cb_limit_period', 'cb_geoip', 'cb_countries',
        'is_api', 'api_secret',
        'smtp_enable', 'smtp_host', 'smtp_port', 'smtp_ssl', 'smtp_user', 'smtp_pass', 'smtp_from',
        'enable_globalcmd',
        'is_featured', 'featured_items', 'is_featured_offer',
        'theme', 'developer_mode',
        'is_maintenance', 'maintenance_ips',
        'developer_mode',
        'is_sale_email_notify',
        'categories_level',
    ];

    public function setEnabledRanksAttribute(array $ranks)
    {
        $this->attributes['enabled_ranks'] = implode(',', $ranks);
    }
}
