<?php

namespace App\Http\Controllers\Admin\Api\Settings;

use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateMerchantConfigRequest;
use App\Models\PaymentMethod;
use App\Models\SecurityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MerchantSettingsController extends Controller
{
    public function update(UpdateMerchantConfigRequest $request, string $merchant): JsonResponse
    {
        if (!UsersController::hasRule('settings', 'write')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $method = PaymentMethod::query()
            ->whereRaw('LOWER(name) = ?', [strtolower($merchant)])
            ->firstOrFail();

        if ($request->has('enable')) {
            // Enable or disable merchant
            $method->update(['enable' => $request->enable]);
            SecurityLog::create([
                'admin_id' => \Auth::guard('admins')->user()->id,
                'method' => SecurityLog::UPDATE_METHOD,
                'action' => SecurityLog::ACTION['merchant'],
                'action_id' => $method->id,
                'extra' => $request->enable == 1 ? 'enabled' : 'disabled',
            ]);
        } else {
            // Update merchant config
            $method->update(['config' => json_encode($request->except(['enable', 'merchant']))]);
            SecurityLog::create([
                'admin_id' => \Auth::guard('admins')->user()->id,
                'method' => SecurityLog::UPDATE_METHOD,
                'action' => SecurityLog::ACTION['merchant'],
                'action_id' => $method->id,
                'extra' => __('has changed merchant settings'),
            ]);
        }

        return response()->json(['message' => __('Changes successfully saved!')]);
    }
}
