<?php

namespace App\Listeners;

use App\Events\ThemeInstalled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendMetricsData
{
    /**
     * Handle the event.
     */
    public function handle(ThemeInstalled $event): void
    {
        // Send POST request to the API about theme installation
        $url = 'https://minestorecms.com/api/colect/metrics/themeInstalled';

        $data = [
            'theme_id' => $event->themeId,
            'domain' => request()->getHost(),
            'date' => date('Y-m-d H:i:s'),
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ],
        ];

        $context = stream_context_create($options);

        @file_get_contents($url, false, $context);
    }
}
