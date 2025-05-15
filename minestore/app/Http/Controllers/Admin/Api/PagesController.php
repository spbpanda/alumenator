<?php

namespace App\Http\Controllers\Admin\Api;

use App\Helpers\SortHelper;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\Controller;
use App\Http\Requests\ReorderStaffItemsRequest;
use App\Http\Requests\Settings\UpdateStaffSettingsRequest;
use App\Models\PlayerData;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PagesController extends Controller
{
    public function __construct()
    {
        $this->loadSettings();
    }

    /**
     * Update settings on Staff Page
     * @param UpdateStaffSettingsRequest $request
     * @return JsonResponse
     */
    public function staffUpdate(UpdateStaffSettingsRequest $request): JsonResponse
    {
        if (!UsersController::hasRule('cms', 'write')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $settings = Setting::find(1);
            $settings->fill($request->validated());
            error_log($settings->is_prefix_enabled);
            $settings->saveOrFail();
        } catch (\Exception) {
            return response()->json(['message' => ''], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['message' => 'Success']);
    }

    /**
     * Update sorting of players and groups
     * @param ReorderStaffItemsRequest $request
     * @return JsonResponse
     */
    public function updateStaffSort(ReorderStaffItemsRequest $request): JsonResponse
    {
        if (!UsersController::hasRule('cms', 'write')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        if ($request->type == 'item') {
            $players = PlayerData::where('player_group', $request->player_group)
                ->orderBy('sorting')
                ->get();

            if ($players->isEmpty()) {
                return response()->json(['message' => 'Players not found'], Response::HTTP_NOT_FOUND);
            }

            // Reorder items
            $player = $players->get($request->old_index);
            $players->forget($request->old_index);
            $players->splice($request->new_index, 0, [$player]);

            SortHelper::reorderItemsAndSave($players);

            return response()->json(['message' => 'Success']);
        }

        $groups = collect(explode(',', $this->settings->enabled_ranks));
        if ($groups->isEmpty()) {
            return response()->json(['message' => 'Groups not found'], Response::HTTP_NOT_FOUND);
        }

        $group = $groups->get($request->old_index);
        $groups->forget($request->old_index);
        $groups->splice($request->new_index, 0, [$group]);

        $this->settings->enabled_ranks = $groups->toArray();
        $this->settings->save();

        return response()->json(['message' => 'Success']);
    }
}
