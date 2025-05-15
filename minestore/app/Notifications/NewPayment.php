<?php

namespace App\Notifications;

use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPayment extends Notification
{
    use Queueable;

    private string $currency;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(private Payment $payment)
    {
        $this->currency = $this->payment->currency ?? '';
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
        $p = $this->payment;
        $name = $p->user->username;
        return [
            'title' => "New Transaction #$p->id",
            'description' => "You got a successful transaction by <b>$name</b> ($p->price $this->currency).",
            'icon' => 'bx-cart',
            'color' => 'success',
            'link' => route('payments.show', $p->id)
        ];
    }
}
