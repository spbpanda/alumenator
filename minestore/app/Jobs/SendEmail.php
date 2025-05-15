<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Configure PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $this->data['settings']['Host'];
            $mail->SMTPAuth = $this->data['settings']['SMTPAuth'];
            $mail->Username = $this->data['settings']['Username'];
            $mail->Password = $this->data['settings']['Password'];
            //$mail->SMTPSecure = $this->data['settings']['SMTPSecure'];
            $mail->Port = $this->data['settings']['Port'];
            $mail->CharSet = 'utf-8';

            $mail->setFrom($this->data['settings']['setFrom'][0], $this->data['settings']['setFrom'][1]);
            $mail->addAddress($this->data['settings']['addAddress']);

            $mail->isHTML(true);
            $mail->Subject = $this->data['settings']['subject'];
            $mail->Body = view($this->data['template'], $this->data['fields'])->render();

            $mail->send();
        } catch (PHPMailerException $e) {
            Log::error('Mail Error PHPMailer: ' . $e->getMessage()); // Log specific error message
        }
    }
}
