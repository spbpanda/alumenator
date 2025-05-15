<?php

namespace App\Events;

use App\Models\Theme;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ThemeInstalled
{

    /**
     * Create a new event instance.
     */

    public $themeId;
    public function __construct($themeId)
    {
        $this->themeId = $themeId;
    }
}
