<?php

namespace App\Http\Controllers;

use App\Events\UpdateAvailable;
use App\Http\Requests\NewServerNotificationRequest;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    public function handleNotification(NewServerNotificationRequest $request): \Illuminate\Http\JsonResponse
    {
        if ($request->event === 'update') {
            $this->sendUpdateNotification($request->data);
        }

        return response()->json(['message' => 'Success']);
    }

    private function sendUpdateNotification(array $data)
    {
        event(new UpdateAvailable($data));
    }
}
