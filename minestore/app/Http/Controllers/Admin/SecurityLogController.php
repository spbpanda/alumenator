<?php

namespace App\Http\Controllers\Admin;

use App\Models\SecurityLog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;

class SecurityLogController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Security Logs'));
    }

    public function index(): View
    {
        if (!UsersController::hasRule('teams', 'read')) {
            return redirect('/admin');
        }

        $logs = SecurityLog::take(20)->get();

        return view('admin.securityLogs.index', compact('logs'));
    }
}
