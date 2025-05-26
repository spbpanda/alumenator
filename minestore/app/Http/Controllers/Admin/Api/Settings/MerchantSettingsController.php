<?php

namespace App\Http\Controllers\Admin\Api\Settings;

use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateMerchantConfigRequest;
use App\Models\PaymentMethod;
use App\Models\PnSetting;
use App\Models\SecurityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class MerchantSettingsController extends Controller
{
    public function update(UpdateMerchantConfigRequest $request, string $merchant): JsonResponse
    {
        if (!UsersController::hasRule('settings', 'write')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Not authorized'
            ], Response::HTTP_FORBIDDEN);
        }

        if (strtolower($merchant) === 'paynow') {
            $pnSettings = PnSetting::first();
            if (!$pnSettings) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'PayNow settings not found. Start onboarding process.'
                ], Response::HTTP_NOT_FOUND);
            }

            $success = $this->managePayNowStatus($request, $pnSettings);
            if (!$success) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to save PayNow settings.'
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Changes successfully saved!'
            ], Response::HTTP_OK);
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

        return response()->json([
            'status' => 'success',
            'message' => __('Changes successfully saved!')
        ], Response::HTTP_OK);
    }

    private function managePayNowStatus(Request $request, PnSetting $pnSettings): bool
    {
        $status = $request->has('enable') ? PnSetting::STATUS_ENABLED : PnSetting::STATUS_DISABLED;
        try {
            $pnSettings->update(['enabled' => $status]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update PayNow settings: ' . $e->getMessage());
            return false;
        }
    }
}
