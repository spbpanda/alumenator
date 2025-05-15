<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreVariableRequest;
use App\Http\Requests\UpdateVariableRequest;
use App\Models\SecurityLog;
use App\Models\Variable;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VarsController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Variables'));
        $this->loadSettings();
    }

    public function index(): View|RedirectResponse
    {
        if (!UsersController::hasRule('variables', 'read')) {
            return redirect('/admin');
        }

        $vars = Variable::query()->where('deleted', 0)->orderBy('id', 'desc')->get();

        return view('admin.vars.index', compact('vars'));
    }

    public function create(): View|RedirectResponse
    {
        if (!UsersController::hasRule('variables', 'write')) {
            return redirect('/admin');
        }

        $isExist = false;
        return view('admin.vars.var', compact('isExist'));
    }

    public function store(StoreVariableRequest $request): RedirectResponse
    {
        if (!UsersController::hasRule('variables', 'write')) {
            return redirect('/admin');
        }

        $data = $request->validated();

        if ($request->type == 0)
            $data['variables'] = array_values($request->input('variables'));

        $variable = Variable::create($data);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::CREATE_METHOD,
            'action' => SecurityLog::ACTION['variables'],
            'action_id' => $variable->id,
        ]);

        return to_route('vars.index');
    }

    public function edit(int $id): View|RedirectResponse
    {
        if (!UsersController::hasRule('variables', 'read')) {
            return redirect('/admin');
        }

        $var = Variable::find($id);
        if (!$var)
            return redirect()->route('vars.index');

        if ($var->deleted == 1)
            return redirect()->route('vars.index');

        $isExist = true;

        return view('admin.vars.var', compact('var', 'isExist'));
    }

    public function update(UpdateVariableRequest $request, int $id): RedirectResponse
    {
        if (!UsersController::hasRule('variables', 'write')) {
            return redirect('/admin');
        }

        $data = $request->validated();

        if ($request->type == 0)
            $data['variables'] = array_values($request->input('variables'));

        Variable::where('id', $id)->where('deleted', 0)->update($data);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['variables'],
            'action_id' => $id,
        ]);

        return to_route('vars.index');
    }

    /**
     * Delete from database
     * @param int $id
     * @return RedirectResponse
     * @return RedirectResponse|JsonResponse
     */

    public function destroy(int $id): RedirectResponse|JsonResponse
    {
        if (!UsersController::hasRule('variables', 'del')) {
            return redirect('/admin');
        }

        try {
            $variable = Variable::find($id);
            $variable->where('id', $id)->update([
                'name' => '[DELETED] ' . $variable->name,
                'deleted' => 1
            ]);

            SecurityLog::create([
                'admin_id' => \Auth::guard('admins')->user()->id,
                'method' => SecurityLog::DELETE_METHOD,
                'action' => SecurityLog::ACTION['variables'],
                'action_id' => $variable->id,
            ]);
        } catch (\Exception $e) {
        }

        if (request()->has('ajax'))
            return response()->json(['status' => 'true']);

        return to_route('vars.index');
    }
}
