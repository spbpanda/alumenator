<?php

namespace App\Http\Controllers\Admin\Api;

use App\Helpers\SortHelper;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\Controller;
use App\Http\Requests\BanUserRequest;
use App\Models\Ban;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NotificationsController extends Controller
{
    /**
     * API endpoint to read the notification
     * @param string $id
     * @return JsonResponse
     */
    public function read(string $id): JsonResponse
    {
        $notification = auth('admins')->user()->notifications()->where('id', $id)->firstOrFail();

        $notification->markAsRead();

        return response()->json(['message' => 'Success']);
    }

    /**
     * API endpoint to read all notifications
     * @return JsonResponse
     */
    public function readAll(): JsonResponse
    {
        $notification = auth('admins')->user()->notifications->markAsRead();

        return response()->json(['message' => 'Success']);
    }

    /**
     * API endpoint to delete notification
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        auth('admins')->user()->notifications()
            ->where('id', $id)
            ->firstOrFail()
            ->delete();


        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
