<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUpdate extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(private array $data)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $d = $this->data;
        $current = config('app.version');
        return [
            'title' => __("New Update"),
            'description' => 'MineStoreCMS released update. New version is v' . $d['version'] .
                ". Your current version is v$current",
            'icon' => 'bxs-cloud-download',
            'color' => 'warning',
            'link' => route('index')
        ];
    }
}
