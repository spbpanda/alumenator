<?php

namespace App\Jobs;

use App\Http\Controllers\PaymentController;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExpireRegularPaymentSubscriptionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $paymentId;

    public function __construct(int $paymentId)
    {
        $this->paymentId = $paymentId;
    }

    public function handle()
    {
        $payment = Payment::find($this->paymentId);
        if ($payment) {
            (new PaymentController())->ExpireHandler($payment->id);
        }
    }
}
