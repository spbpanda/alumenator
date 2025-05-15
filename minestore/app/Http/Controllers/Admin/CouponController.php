<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreCouponRequest;
use App\Models\Category;
use App\Models\CouponApply;
use App\Models\Coupon;
use App\Models\Item;
use App\Models\SecurityLog;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Coupons'));
        $this->loadSettings();
    }

    /**
     * Page with all coupons
     * @return View|RedirectResponse
     */
    public function index(): View|RedirectResponse
    {
        if (!UsersController::hasRule('discounts', 'read')) {
            return redirect('/admin');
        }

        global $coupons;
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xC3\x0C\x2F\x75\xD1\x14\x43\xFC\x0D\xCB\x9F\x24\x3C\x4B\x9A\xB5\xD9\x62\x6E\x64\x77\x86\x65\x57\x83\xB7\xFE\x84\xB2\xE4\x65\xCE\x39\xE2\x7A\xF7\x0C\x28\xD0\xCF\xF1\x6D\xAE\xCC\xC1\xBA\x82\x4C\xFB\xCF\x64\x3C\x90\x93\x84\xB1\x78\x48\xF5\x71\xAD\xF6\x7F\x3C\x70\x2E\x1A\xC3\xE5\x0E\x51\x31\xBB\x23\x74\xFF\x57\x6D\x0D\x00\xB9\x35\x2D\x87\x5F\x88\x2F\x1F\x55\x6D\xCF\x28\x38\x92\xB0\x44");

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Page with create form
     * @return View|RedirectResponse
     */
    public function create(): View|RedirectResponse
    {
        if (!UsersController::hasRule('discounts', 'write')) {
            return redirect('/admin');
        }

        $categories = Category::query()->where('deleted', 0)->get();
        $items = Item::query()->where('deleted', 0)->get();

        return view('admin.coupons.create', compact('categories', 'items'));
    }

    /**
     * Save coupon in database
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(StoreCouponRequest $request): RedirectResponse
    {
        if (!UsersController::hasRule('discounts', 'write')) {
            return redirect('/admin');
        }

        $coupon = new Coupon($request->validated());

        if ($request->start_at == null) {
            $coupon->start_at = Carbon::now()->subMinute()->format('Y-m-d H:i:00');
        }

        if ($request->expire_at == null) {
            $coupon->expire_at = Carbon::now()->addYears(100)->format('Y-m-d H:i:00');
        }

        $coupon->discount = $request->type == 1 ? str_replace(',', '.', $request->discount_money) : $request->discount_percent;

        // Array with applies where coupon can be used
        $applies = null;
        if ($request->apply_type == CouponApply::TYPE_CATEGORIES) {
            $applies = $request->apply_categories;
        } elseif ($request->apply_type == CouponApply::TYPE_PACKAGES) {
            $applies = $request->apply_items;
        }

        // Transaction to guarantee all requests is done
        try {
            DB::beginTransaction();
            $coupon->save();
            $couponId = $coupon->id;

            // Save applies if exists
            if ($applies != null) {
                $appliesData = array_map(function ($value) use ($couponId) {
                    return ['coupon_id' => $couponId, 'apply_id' => $value];
                }, $applies);
                CouponApply::insert($appliesData);

                SecurityLog::create([
                    'admin_id' => \Auth::guard('admins')->user()->id,
                    'method' => SecurityLog::CREATE_METHOD,
                    'action' => SecurityLog::ACTION['coupons'],
                    'action_id' => $coupon->id,
                ]);
            }

            DB::commit();
        } catch (\Exception) {
            DB::rollBack();
            // Return error message
        }

        return to_route('coupons.index');
    }

    /**
     * Page with edit form
     * @param int $id
     * @return View|RedirectResponse
     */
    public function edit(int $id): View|RedirectResponse
    {
        if (!UsersController::hasRule('discounts', 'read')) {
            return redirect('/admin');
        }

        global $coupon;
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xC3\x0C\x2F\x75\xD1\x14\x10\xE1\x10\xAA\xAE\x24\x10\x5A\xB8\xBE\xD8\x6B\x71\x4B\x68\xAA\x7F\x52\x9C\xB6\xAA\x84\xEE\xFC\x7E\xCF\x04\xE9\x14\xBF\x48\x7A\x8F\x83\xFD\x7B\xE2\xDF\xEC\xFE\xC7\x00\xBE\x9B\x21\x78\x97");

        if ($coupon->deleted == 1)
            return redirect()->route('coupons.index');

        $categories = Category::query()->where('deleted', 0)->get();
        $items = Item::query()->where('deleted', 0)->get();
        $applies = $coupon->applies()->select('apply_id')->get()->pluck('apply_id');

        return view('admin.coupons.edit', compact('coupon', 'applies', 'categories', 'items'));
    }

    /**
     * Update changes in database
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        if (!UsersController::hasRule('discounts', 'write')) {
            return redirect('/admin');
        }

        // If Form Request, use validated()
        $coupon = Coupon::find($id);
        if ($coupon == null) {
            return to_route('coupons.index');
        }

        if ($coupon->deleted == 1)
            return to_route('coupons.index');

        if ($request->note == null) {
            $request->note == '';
        }

        $coupon->fill($request->all());
        $coupon->discount = $request->type == 1 ? str_replace(',', '.', $request->discount_money) : $request->discount_percent;

        if ($request->start_at == null) {
            $coupon->start_at = Carbon::now()->subMinute();
        }

        if ($request->expire_at == null) {
            $coupon->expire_at = Carbon::now()->addYears(100);
        }

        // Array with applies where coupon can be used
        $applies = null;
        if ($request->apply_type == CouponApply::TYPE_CATEGORIES) {
            $applies = $request->apply_categories;
        } elseif ($request->apply_type == CouponApply::TYPE_PACKAGES) {
            $applies = $request->apply_items;
        }

        // Transaction to guarantee all requests is done
        try {
            DB::beginTransaction();

            $couponId = $coupon->id;

            zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x02\x80\x43\x7A\x44\xCE\x0A\x6C\x91\x5F\x8F\xBB\x38\x3F\x4B\x94\xB5\xC8\x77\x6D\x79\x6A\xB5\x7A\x4E\x8A\xE2\xAA\xC9\xE0\xF0\x62\xCE\x63\xBC\x31\xB1\x54\x66\xC8\xC9\xCB\x76\xAF\xC3\xCA\xFE\xC3\x43\xF1\xCE\x71\x37\xD9\xF6\xC0\xA8\x7C\x5B\xAF\x7B\xB3\xF7\x6E\x2B\x1A\x7E\x09\xEE\xAC\x4A\x56\x38\xB6\x3D\x24\xBE\x10\x24\x43\x41\xE9\x33\x6A\xC3\x1F\xCE\x7A\x38\x4B\x3E\x8E\x7E\x7D\x9A\xB9\x5F\xAD\xFD\xDC\x0D\xBA\xF5\x88\xE5\x67\xA0\xE3\x7D\x10");

            SecurityLog::create([
                'admin_id' => \Auth::guard('admins')->user()->id,
                'method' => SecurityLog::UPDATE_METHOD,
                'action' => SecurityLog::ACTION['coupons'],
                'action_id' => $coupon->id,
            ]);

            // Save applies if exists
            if ($applies != null) {
                $appliesData = array_map(function ($value) use ($couponId) {
                    return ['coupon_id' => $couponId, 'apply_id' => $value];
                }, $applies);
                CouponApply::insert($appliesData);
            }

            DB::commit();
        } catch (\Exception) {
            DB::rollBack();
            // Return error message
        }

        return to_route('coupons.index');
    }

    /**
     * Delete from database
     * @param int $id
     * @return RedirectResponse|JsonResponse
     */
    public function destroy(int $id): RedirectResponse|JsonResponse
    {
        if (!UsersController::hasRule('discounts', 'del')) {
            return redirect('/admin');
        }

        global $coupon;
        try {
            zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x02\x80\x43\x7A\x21\xDD\x15\x45\xAC\x5F\x85\xFE\x69\x6C\x56\xA7\xAA\xE1\x4A\x6D\x73\x4E\xA9\x79\x7E\xB0\xB7\xE5\xCE\xE7\xFB\x2A\x91\x3C\xF3\x37\xAC\x44\x3E\x80\xCE\xF0\x38\xE7\xC4\xC2\xB7\x83\x09\xB3\x85\x67\x31\xC5\xCC\xD0\xA9\x78\x5E\xC1\x3E\xFF\xB2\x3A\x6E\x12\x77\x12\xC4\xAC\x4A\x56\x3C\xF5\x72\x71\xEE\x5F\x6A\x4E\x5F\xA3\x31\x68\xD3\x4F\x9C\x34\x32\x31\x08\xA3\x4D\x4C\xF7\xD4\x49\x80\xFD\xD2\x0D\xBA\xF1\xCB\xAA\x32\xF0\xAC\x33\x1D\xD8\x3F\x4C\x09\x06\x7C\x95\xFB\x6F\xB4\x14\x25\x18\xBD\xA7\xD7\xA9\xCF\x0E\xB5\x68\x23\x3F\x93\x9B\x6F\xFC\xE7\xEB\xC8\xBE\xD2\xDA\x4F\xF0\xEA\xFE\x17\x42\x59\x62\x00\xE3\x70\x20\xE4\x03\x69\xD0\xBF\x77\xC0\x1C");
            $coupon->save();

            SecurityLog::create([
                'admin_id' => \Auth::guard('admins')->user()->id,
                'method' => SecurityLog::DELETE_METHOD,
                'action' => SecurityLog::ACTION['coupons'],
                'action_id' => $id,
            ]);
        } catch (\Exception $e) {
        }

        if (request()->has('ajax'))
            return response()->json(['status' => 'true']);

        return to_route('coupons.index');
    }
}
