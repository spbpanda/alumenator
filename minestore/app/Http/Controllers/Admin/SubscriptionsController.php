<?php

namespace App\Http\Controllers\Admin;

use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionsController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Subscriptions'));
    }

    public function index()
    {
        if (! UsersController::hasRule('subs', 'read')) {
            return redirect('/admin');
        }

        $subscriptions = Subscription::orderBy('id', 'desc')->get();

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function datatables(Request $r)
    {
        if (!UsersController::hasRule('subs', 'read')) {
            return redirect('/admin');
        }

        $startIndex = (int)$r->input('start');
        $length = (int)$r->input('length');
        $currentPage = 1 + ($startIndex == 0 ? 0 : ($startIndex / $length));
        \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });
        $subs = Subscription::query()
        ->with(['user' => function ($query) {
            $query->select('username');
        }])
        ->with(['payment' => function ($query) {
            $query->select('id', 'gateway');
        }])->orderBy('id', 'DESC')->paginate($length);

        return [
            "draw" => (int)$r->input('draw'),
            "recordsTotal" => $subs->total(),
            "recordsFiltered" => $subs->total(),
            "data" => $subs->items(),
        ];
    }
}
