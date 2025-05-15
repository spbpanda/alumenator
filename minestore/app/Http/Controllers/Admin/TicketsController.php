<?php

namespace App\Http\Controllers\Admin;

class TicketsController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Tickets'));
    }

    public function index()
    {
        $tickets = '';

        return view('admin.tickets.index', compact('tickets'));
    }

    public function response()
    {
        $tickets = '';

        return view('admin.tickets.response', compact('tickets'));
    }
}
