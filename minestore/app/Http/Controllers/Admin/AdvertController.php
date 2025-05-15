<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreAdvertRequest;
use App\Models\Advert;
use App\Models\SecurityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdvertController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Announcement Settings'));
    }

    public function index(): View|RedirectResponse
    {
        if (!UsersController::hasRule('announcement', 'read')) {
            return redirect('/admin');
        }

        $advert = \App\Http\Controllers\AdvertController::getAdvert();

        return view('admin.advert.index', compact('advert'));
    }

    public function store(StoreAdvertRequest $request): RedirectResponse
    {
        if (!UsersController::hasRule('announcement', 'write')) {
            return redirect('/admin');
        }

        Advert::where('id', 1)->update($request->validated());

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['announcement'],
        ]);

        return to_route('advert.index');
    }
}
