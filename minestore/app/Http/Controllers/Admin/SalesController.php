<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SalesHelper;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Models\Advert;
use App\Models\Category;
use App\Models\Item;
use App\Models\SaleApply;
use App\Models\Sale;
use App\Models\SaleCommand;
use App\Models\SecurityLog;
use App\Models\Variable;
use App\Models\ItemServer;
use App\Models\Server;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SalesController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Sales Settings'));
        $this->loadSettings();
    }

    public function index(): View|RedirectResponse
    {
        if (!UsersController::hasRule('discounts', 'read')) {
            return redirect('/admin');
        }

        $sales = Sale::all()->reverse();
        $is_sale_email_notify = false;

        return view('admin.sales.index', compact('sales', 'is_sale_email_notify'));
    }

    public function create(): View|RedirectResponse
    {
        if (!UsersController::hasRule('discounts', 'write')) {
            return redirect('/admin');
        }

        $items = Item::where('deleted', 0)->get();
        $categories = Category::where('deleted', 0)->get();
        $vars = Variable::where('deleted', 0)->get();
        $servers = Server::where('deleted', 0)->get();

        return view('admin.sales.create', compact('items', 'categories', 'vars', 'servers'));
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        if (!UsersController::hasRule('discounts', 'write')) {
            return redirect('/admin');
        }

        $sale = new Sale($request->validated());

        $packages_commands = [];

        // Save packages commands
        if ($request->has('packages_commands') && !empty($request->input('packages_commands')))
        {
            foreach ($request->input('packages_commands') as $item)
                $packages_commands[] = $item;

            $packages_commands_temp = $packages_commands;
            unset($packages_commands_temp['servers']);
            $sale->packages_commands = json_encode($packages_commands_temp);
            $sale->save();
        } else {
            // Save the sale if packages_commands were not added
            $packages_commands_temp = $packages_commands;
            unset($packages_commands_temp['servers']);
            $sale->packages_commands = json_encode($packages_commands_temp);
            $sale->save();
        }

        // Array with applies where coupon can be used
        $applies = SalesHelper::getApplies($request->validated());

        // Transaction to guarantee all requests is done
        try {
            DB::beginTransaction();

            // Save applies if exists
            if ($applies != null) {
                $applyType = SalesHelper::getApplyType($request->apply_type);
                $applies = collect($applies)->map(function ($value) use ($applyType, $sale) {
                    return ['apply_type' => $applyType, 'apply_id' => $value, 'sale_id' => $sale->id];
                });
                $sale->applies()->createMany($applies);
            }

            if ($request->start_at == null) {
                $sale->start_at = Carbon::now();
                $sale->is_enable = 1;
            }

            if ($request->start_at > Carbon::now()) {
                $sale->is_enable = 0;
            }

            $sale->save();

            foreach ($packages_commands as $packages_command) {
                $saleCommand = SaleCommand::create([
                    'sale_id' => $sale->id,
                    'item_id' => $packages_command['item_id'],
                    'command' => $packages_command['command'],
                ]);

                $servers = [];
                if (!empty($packages_command['servers']) && is_array($packages_command['servers']) && !in_array('ALL', $packages_command['servers'])) {
                    $servers = array_values($packages_command['servers']);
                }

                foreach ($servers as $server){
                    if (empty($server) || $server == 'ALL')
                        continue;

                    ItemServer::create([
                        'type' => ItemServer::TYPE_SALE_COMMAND_SERVER,
                        'item_id' => $saleCommand->id,
                        'server_id' => $server,
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
        }

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::CREATE_METHOD,
            'action' => SecurityLog::ACTION['sales'],
            'action_id' => $sale->id,
        ]);

        return to_route('sales.index');
    }

    public function edit(int $id): View|RedirectResponse
    {
        if (!UsersController::hasRule('discounts', 'read')) {
            return redirect('/admin');
        }

        $sale = Sale::findOrFail($id);
        $items = Item::where('deleted', 0)->get();
        $categories = Category::where('deleted', 0)->get();
        $vars = Variable::where('deleted', 0)->get();
        $servers = Server::where('deleted', 0)->get();

        return view('admin.sales.edit', compact('sale', 'categories', 'items', 'vars', 'servers'));
    }

    public function update(UpdateSaleRequest $request, int $id): RedirectResponse
    {
        if (!UsersController::hasRule('discounts', 'write')) {
            return redirect('/admin');
        }

        $sale = Sale::findOrFail($id);
        $originalApplyType = $sale->apply_type;
        $sale->fill($request->validated());

        $packages_commands = [];

        // Save packages commands
        if ($request->has('packages_commands') && !empty($request->input('packages_commands'))) {
            foreach ($request->input('packages_commands') as $item)
                $packages_commands[] = $item;

            $packages_commands_temp = $packages_commands;
            unset($packages_commands_temp['servers']);
            $sale->packages_commands = json_encode($packages_commands_temp);
            $sale->save();
        } else {
            // Save the sale if packages_commands were not added
            $packages_commands_temp = $request->input('packages_commands');
            unset($packages_commands_temp['servers']);
            $sale->packages_commands = json_encode($packages_commands_temp);
            $sale->save();
        }

        $applies = SalesHelper::getApplies($request->validated());

        if ($request->apply_type !== SaleApply::TYPE_WHOLE_STORE && $applies != null) {
            $currentSaleApplies = $sale->applies()->pluck('apply_id')->toArray();
            $appliesToRemove = array_diff($currentSaleApplies, $applies);
        }

        if ($request->apply_type == $originalApplyType && !empty($appliesToRemove) && $applies != null) {
            $this->resetSaleApplies($sale, $appliesToRemove);
        }

        // Transaction to guarantee all requests is done
        try {
            DB::beginTransaction();
            $sale->save();

            // Save applies if exists
            if ($applies != null) {
                $sale->applies()->delete();

                $applyType = SalesHelper::getApplyType($request->apply_type);
                $applies = collect($applies)->map(function ($value) use ($applyType, $sale) {
                    return ['apply_type' => $applyType, 'apply_id' => $value, 'sale_id' => $sale->id];
                });

                $sale->applies()->createMany($applies);
            }

            switch ($request->start_at) {
                case null:
                    $sale->start_at = Carbon::now();
                    $sale->is_enable = 1;
                    $this->updateApplies($sale);
                    break;
                case $request->start_at < Carbon::now():
                    $sale->is_enable = 1;
                    $this->updateApplies($sale);
                    break;
                case $request->start_at > Carbon::now():
                    $sale->is_enable = 0;
                    break;
            }

            $sale->save();

            SaleCommand::where('sale_id', $id)->delete();

            foreach ($packages_commands as $packages_command) {
                $saleCommand = SaleCommand::create([
                    'sale_id' => $sale->id,
                    'item_id' => $packages_command['item_id'],
                    'command' => $packages_command['command'],
                ]);

                $servers = [];
                if (!empty($packages_command['servers']) && is_array($packages_command['servers']) && !in_array('ALL', $packages_command['servers'])) {
                    $servers = array_values($packages_command['servers']);
                }

                ItemServer::where([['type', ItemServer::TYPE_SALE_COMMAND_SERVER], ['item_id', $saleCommand->id]])->delete();
                foreach ($servers as $server){
                    if (empty($server) || $server == 'ALL')
                        continue;

                    ItemServer::create([
                        'type' => ItemServer::TYPE_SALE_COMMAND_SERVER,
                        'item_id' => $saleCommand->id,
                        'server_id' => $server,
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating the sale: ' . $e->getMessage());
        }

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['sales'],
            'action_id' => $id,
        ]);

        return to_route('sales.edit', $sale->id);
    }

    /**
     * Update the applies for a given sale.
     *
     * This method updates the discount for items based on the sale's apply type.
     * It handles three types of applies: whole store, categories, and packages.
     *
     * @param \App\Models\Sale $sale The sale instance containing the apply type and discount information.
     *
     * @return void
     */
    private function updateApplies(Sale $sale): void
    {
        switch ($sale->apply_type) {
            case SaleApply::TYPE_WHOLE_STORE:
                $items = Item::where('deleted', 0)->get();
                foreach ($items as $item) {
                    $item->update([
                        'discount' => $sale->discount,
                    ]);
                }
                break;
            case SaleApply::TYPE_CATEGORIES:
                $applies = $sale->applies()->pluck('apply_id')->toArray();
                foreach ($applies as $apply) {
                    $category = Category::where('id', $apply)
                        ->where('deleted', 0)
                        ->first();

                    if ($category) {
                        $items = $category->packages()->get();
                        foreach ($items as $item) {
                            $item->update([
                                'discount' => $sale->discount,
                            ]);
                        }
                    }
                }
                break;
            case SaleApply::TYPE_PACKAGES:
                $applies = $sale->applies()->pluck('apply_id')->toArray();
                foreach ($applies as $apply) {
                    $item = Item::where('id', $apply)
                        ->where('deleted', 0)
                        ->first();
                    if ($item) {
                        $item->update([
                            'discount' => $sale->discount,
                            'featured' => 1
                        ]);
                    }
                }
                break;
        }
    }

    /**
     * Reset the applies for a given sale.
     *
     * This method resets the discount for items based on the sale's apply type.
     * It handles three types of applies: whole store, categories, and packages.
     *
     * @param \App\Models\Sale $sale The sale instance containing the apply type and discount information.
     * @param array $appliesToRemove The applies to remove from the sale.
     *
     * @return void
     */
    private function resetSaleApplies(Sale $sale, array $appliesToRemove): void
    {
        switch ($sale->apply_type) {
            case SaleApply::TYPE_CATEGORIES:
                foreach ($appliesToRemove as $apply) {
                    $category = Category::where('id', $apply)
                        ->where('deleted', 0)
                        ->first();

                    if ($category) {
                        $items = $category->packages()->get();
                        foreach ($items as $item) {
                            $item->update([
                                'discount' => 0,
                            ]);
                        }
                    }
                }
                break;
            case SaleApply::TYPE_PACKAGES:
                foreach ($appliesToRemove as $apply) {
                    $item = Item::where('id', $apply)
                        ->where('deleted', 0)
                        ->first();
                    $item?->update([
                        'discount' => 0,
                        'featured' => 0
                    ]);
                }
                break;
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        if (!UsersController::hasRule('discounts', 'del')) {
            return redirect('/admin');
        }

        $sale = Sale::where('id', $id)->first();
        if ($sale) {
            switch ($sale->apply_type) {
                case SaleApply::TYPE_CATEGORIES:
                    $applies = $sale->applies()->pluck('apply_id')->toArray();
                    foreach ($applies as $apply) {
                        $category = Category::where('id', $apply)
                            ->where('deleted', 0)
                            ->first();

                        if ($category) {
                            $items = $category->packages()->get();
                            foreach ($items as $item) {
                                $item->update([
                                    'discount' => 0,
                                ]);
                            }
                        }
                    }
                    break;
                case SaleApply::TYPE_PACKAGES:
                    $applies = $sale->applies()->pluck('apply_id')->toArray();
                    foreach ($applies as $apply) {
                        $item = Item::where('id', $apply)
                            ->where('deleted', 0)
                            ->first();
                        if ($item) {
                            $item->update([
                                'discount' => 0,
                                'featured' => 0
                            ]);
                        }
                    }
                    break;
                case SaleApply::TYPE_WHOLE_STORE:
                    $items = Item::where('deleted', 0)->get();
                    foreach ($items as $item) {
                        $item->update([
                            'discount' => 0,
                        ]);
                    }
                    break;
            }

            if ($sale->is_advert == 1) {
                Advert::where('id', 1)->update([
                    'is_index' => 0,
                ]);
            }
        }
        SaleCommand::where('sale_id', $sale->id)->delete();
        SaleApply::where('sale_id', $sale->id)->delete();
        ItemServer::where([['type', ItemServer::TYPE_SALE_COMMAND_SERVER], ['item_id', $sale->id]])->delete();
        $sale->delete();

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::DELETE_METHOD,
            'action' => SecurityLog::ACTION['sales'],
            'action_id' => $id,
        ]);

        return redirect('/admin/sales');
    }
}
