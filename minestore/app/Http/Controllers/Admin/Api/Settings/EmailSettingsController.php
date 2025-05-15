<?php

namespace App\Http\Controllers\Admin\Api\Settings;

use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\Controller;
use Crypt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class EmailSettingsController extends Controller
{
    public function __construct()
    {
        $this->loadSettings();
    }

    /**
     * Check SMTP connection
     * @param string $id
     * @return JsonResponse
     */
    public function check(): JsonResponse
    {
        if (!UsersController::hasRule('settings', 'read')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $config = $this->settings->only(['smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass']);

        try {
            $transport = new EsmtpTransport($config['smtp_host'], $config['smtp_port']);
            $transport->setUsername($config['smtp_user']);
            $transport->setPassword(Crypt::decryptString($config['smtp_pass']));
            $transport->start();

            // The SMTP connection test was successful
            return response()->json(['message' => 'Success']);
        } catch (\Exception $e) {
            Log::error('SMTP connection test failed: ' . $e->getMessage());
            // The SMTP connection test failed
            return response()->json([
                'message' => __('Failed to connect the SMTP server')
            ], Response::HTTP_BAD_GATEWAY);
        }
    }
}
