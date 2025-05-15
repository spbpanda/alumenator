<?php

namespace App\Notifications;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MonthlyReport extends Notification
{
    use Queueable;

    private string $currency;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(private string $month, private string $revenue)
    {
        $this->currency = Setting::find(1)->currency ?? '';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
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
            'title' => __("Monthly Report"),
            'description' => "$this->month is over. Your revenue was <b>$this->revenue $this->currency</b>.",
            'icon' => 'bxs-report',
            'color' => 'primary',
            'link' => route('statistics.index')
        ];
    }
}
