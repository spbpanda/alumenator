<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Settings\StorePromotedSettingsRequest;
use App\Http\Requests\StorePromotedItemRequest;
use App\Models\PromotedItem;
use App\Models\Item;
use App\Models\SecurityLog;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PromotedItemsController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Promoted Packages'));
        $this->loadSettings();
    }

    public function index(): View|RedirectResponse
    {
        if (!UsersController::hasRule('promoted_packages', 'read')) {
            return redirect('/admin');
        }

        $promotedItems = PromotedItem::orderBy('order')->get();
        $items = Item::query()
            ->where('deleted', 0)
            ->where('active', 1)
            ->get();

        return view('admin.promoted.index', compact('promotedItems', 'items'));
    }

    public function create(): View|RedirectResponse
    {
        if (!UsersController::hasRule('promoted_packages', 'write')) {
            return redirect('/admin');
        }

        $items = Item::where('deleted', 0)
            ->where('active', 1)
            ->get();

        return view('admin.promoted.create', compact('items'));
    }

    public function store(StorePromotedItemRequest $request): RedirectResponse
    {
        if (!UsersController::hasRule('promoted_packages', 'write')) {
            return redirect('/admin');
        }

        $promotedItem = PromotedItem::create($request->validated());

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::CREATE_METHOD,
            'action' => SecurityLog::ACTION['promoted_packages'],
            'action_id' => $promotedItem->id,
        ]);

        return to_route('promoted.index');
    }

    public function destroy(int $id): RedirectResponse
    {
        if (!UsersController::hasRule('promoted_packages', 'write')) {
            return redirect('/admin');
        }

        PromotedItem::where('id', $id)->delete();

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::DELETE_METHOD,
            'action' => SecurityLog::ACTION['promoted_packages'],
            'action_id' => $id,
        ]);

        return to_route('promoted.index');
    }

    /**
     * Update promoted settings via AJAX request
     *
     * @param StorePromotedSettingsRequest $request
     * @return JsonResponse
     */
    public function settings(StorePromotedSettingsRequest $request): JsonResponse
    {
        if (!UsersController::hasRule('promoted_packages', 'write')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $settings = Setting::find(1);
            $settings->fill($request->validated());
            $settings->saveOrFail();
        } catch (\Exception) {
            return response()->json(['message' => ''], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['message' => 'Success']);
    }
}
