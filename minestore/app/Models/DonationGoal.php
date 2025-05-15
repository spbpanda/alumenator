<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DonationGoal extends Model
{
    use HasFactory;

    protected $table = 'donation_goals';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'status',
        'is_enabled',
        'automatic_disabling',
        'current_amount',
        'goal_amount',
        'cmdExecute',
        'commands_to_execute',
        'packages_commands',
        'servers',
        'reached_at',
        'start_at',
        'disable_at'
    ];
}
