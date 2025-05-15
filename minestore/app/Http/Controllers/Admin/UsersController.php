<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\SecurityLog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    private const RULES = [
        //[label, column in db]
        ['Homepage', 'dashboard'],
        ['Packages (Categories \ Packages \ Featured Deals)', 'packages'],
        ['Promoted Packages', 'promoted_packages'],
        ['Variables', 'variables'],
        ['Settings', 'settings'],
        ['Engagement', 'discounts'],
        ['CMS Settings', 'cms'],
        // ['Tickets', 'tickets'],
        ['Player Referrals', 'referers'],
        ['Transactions', 'payments'],
        ['Subscriptions', 'subs'],
        ['Fraud', 'fraud'],
        ['Taxes', 'taxes'],
        ['Announcement', 'announcement'],
        ['Bans \ Whitelist \ Lookup', 'bans'],
        ['User Lookup (Checking Permissions)', 'lookup'],
        ['Global Commands', 'global_commands'],
        ['Teams', 'teams'],
        ['Statistics', 'statistics'],
        ['API Settings', 'api'],
        ['Themes', 'themes'],
        ['Donation Goals', 'donation_goals'],
    ];

    public static function hasRule($ruleName, $ruleType)
    {
        $_rules = json_decode(\Auth::guard('admins')->user()->rules, true);

        return $_rules['isAdmin'] || (isset($_rules[$ruleName]) && isset($_rules[$ruleName][$ruleType]) && $_rules[$ruleName][$ruleType]);
    }

    public static function adminsWithRule($name, $type): Collection
    {
        $admins = Admin::all();
        $admins->filter(function ($admin) use ($type, $name) {
            return UsersController::userHasRule($admin, $name, $type);
        });

        return $admins;
    }

    private static function userHasRule($user, $ruleName, $ruleType): bool
    {
        $_rules = json_decode($user->rules, true);

        return $_rules['isAdmin'] || (isset($_rules[$ruleName]) && isset($_rules[$ruleName][$ruleType]) && $_rules[$ruleName][$ruleType]);
    }

    public function __construct()
    {
        $this->setTitle('Users');
    }

    public function index(): View|RedirectResponse
    {
        if (!UsersController::hasRule('teams', 'read')) {
            return redirect('/admin');
        }

        $users = Admin::select(['id', 'username', 'rules', 'is_2fa', 'last_login_time', 'created_at', 'updated_at'])
            ->orderBy('id')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View|RedirectResponse
    {
        if (!UsersController::hasRule('teams', 'write')) {
            return redirect('/admin');
        }

        return view('admin.users.create', ['rules' => $this::RULES]);
    }

    public function store(Request $r): RedirectResponse
    {
        if (!UsersController::hasRule('teams', 'write')) {
            return redirect('/admin');
        }

        $r->validate([
            'username' => 'required|string|max:255',
            'password' => [
                'required',
                'string',
                'min:8',
            ],
        ]);

        $rules = $this->makeRules($r->all());

        $userData = [
            'username' => $r->input('username'),
            'rules' => json_encode($rules),
        ];
        if (!empty($r->input('password'))) {
            $userData['password'] = Hash::make($r->input('password'));
        }

        $admin = Admin::create($userData);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::CREATE_METHOD,
            'action' => SecurityLog::ACTION['teams'],
            'action_id' => $admin->id,
        ]);

        return to_route('users.index');
    }

    public function show(string $id): View|RedirectResponse
    {
        if (!UsersController::hasRule('teams', 'read')) {
            return redirect('/admin');
        }

        $user = Admin::findOrFail($id);
        $userRules = json_decode($user->rules, true);

        return view('admin.users.edit', [
            'user' => $user,
            'rules' => $this::RULES,
            'userRules' => $userRules
        ]);
    }

    public function update(Request $r, string $id): RedirectResponse
    {
        if (!UsersController::hasRule('teams', 'write')) {
            return redirect('/admin');
        }

        $r->validate([
            'username' => 'required|string|max:255',
            'password' => [
                'sometimes',
                'nullable',
                'string',
                'min:8',
            ],
        ]);

        $rules = $this->makeRules($r->all());

        $userData = [
            'username' => $r->input('username'),
            'rules' => json_encode($rules),
        ];

        if (!empty($r->input('password'))) {
            $userData['password'] = Hash::make($r->input('password'));
        }

        Admin::where('id', $id)->update($userData);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['teams'],
            'action_id' => $id,
        ]);

        return to_route('users.index');
    }

    /**
     * Delete from database
     * @param int $id
     * @return RedirectResponse
     * @return RedirectResponse|JsonResponse
     */

    public function destroy(int $id): RedirectResponse|JsonResponse
    {
        if (!UsersController::hasRule('teams', 'del')) {
            return redirect('/admin');
        }

        Admin::destroy($id);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::DELETE_METHOD,
            'action' => SecurityLog::ACTION['teams'],
            'action_id' => $id,
        ]);

        if (request()->has('ajax'))
            return response()->json(['status' => 'true']);

        return to_route('users.index');
    }

    private function makeRules($array)
    {
        if (isset($array['is_admin']) && $array['is_admin'] == 'on') {
            return ['isAdmin' => true];
        }

        $result = ['isAdmin' => false];
        for ($i = 0; $i < count($this::RULES); $i++) {
            $result[$this::RULES[$i][1]]['read'] = false;
            $result[$this::RULES[$i][1]]['write'] = false;
            $result[$this::RULES[$i][1]]['del'] = false;
            if (isset($array[$this::RULES[$i][1]])) {
                if (isset($array[$this::RULES[$i][1]]['read']) && $array[$this::RULES[$i][1]]['read'] == 'on') {
                    $result[$this::RULES[$i][1]]['read'] = true;
                }

                if (isset($array[$this::RULES[$i][1]]['write']) && $array[$this::RULES[$i][1]]['write'] == 'on') {
                    $result[$this::RULES[$i][1]]['write'] = true;
                }

                if (isset($array[$this::RULES[$i][1]]['del']) && $array[$this::RULES[$i][1]]['del'] == 'on') {
                    $result[$this::RULES[$i][1]]['del'] = true;
                }
            }
        }

        return $result;
    }
}
