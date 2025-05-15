<?php

namespace App\Notifications;

use App\Models\Chargeback;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewChargeback extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(private Chargeback $chargeback)
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
        return [
            'title' => __('Chargeback Case #') . $this->chargeback->id,
            'description' => 'Chargeback by <b>' . $this->chargeback->user->username .
                '</b> for reason <b>' . $this->chargeback->getDetails()->reason . '</b>.',
            'icon' => 'bx-money-withdraw',
            'color' => 'danger',
            'link' => route('chargeback.show', $this->chargeback->id)
        ];
    }
}
