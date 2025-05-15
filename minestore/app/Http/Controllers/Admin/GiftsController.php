<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreGiftRequest;
use App\Http\Requests\UpdateGiftRequest;
use App\Models\Gift;
use App\Models\SecurityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class GiftsController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Gift Cards'));
        $this->loadSettings();
    }

    public function index(): View|RedirectResponse
    {
        if (!UsersController::hasRule('discounts', 'read')) {
            return redirect('/admin');
        }

        $gifts = Gift::where('deleted', 0)->orderBy('id')->get();

        return view('admin.gifts.index', compact('gifts'));
    }

    public function create(): View|RedirectResponse
    {
        if (!UsersController::hasRule('discounts', 'write')) {
            return redirect('/admin');
        }

        return view('admin.gifts.create');
    }

    public function store(StoreGiftRequest $request): RedirectResponse
    {
        if (!UsersController::hasRule('discounts', 'read')) {
            return redirect('/admin');
        }

        $data = $request->validated();
        $data['end_balance'] = $request->start_balance;
        $data['note'] = $data['note'] ?? '';

        $gift = Gift::create($data);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::CREATE_METHOD,
            'action' => SecurityLog::ACTION['giftcards'],
            'action_id' => $gift->id,
        ]);

        return to_route('gifts.index');
    }


    public function edit(int $id): View|RedirectResponse
    {
        if (!UsersController::hasRule('discounts', 'read')) {
            return redirect('/admin');
        }

        $gift = Gift::findOrFail($id);

        if ($gift->deleted == 1)
            return redirect()->route('gifts.index');

        return view('admin.gifts.edit', compact('gift'));
    }

    public function update(UpdateGiftRequest $request, int $id): RedirectResponse
    {
        if (!UsersController::hasRule('discounts', 'read')) {
            return redirect('/admin');
        }

        $request['note'] = $request['note'] ?? '';

        Gift::where('id', $id)->update($request->validated());


        return to_route('gifts.index');
    }
    /**
     * Delete from database
     * @param int $id
     * @return RedirectResponse
     * @return RedirectResponse|JsonResponse
     */

    public function destroy(int $id): RedirectResponse|JsonResponse
    {
        if (!UsersController::hasRule('discounts', 'del')) {
            return redirect('/admin');
        }

        $gift = Gift::find($id);

        if (!$gift) {
            return response()->json(['status' => 'false', 'message' => __('Record not found')]);
        }

        $gift->update([
            'name' => 'DELETED-' . $gift->name,
            'deleted' => 1
        ]);

        if (request()->has('ajax')) {
            return response()->json(['status' => 'true']);
        }

        return to_route('gifts.index');
    }
}
