<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\ItemComparison;
use App\Models\Comparison;
use App\Models\Item;
use App\Models\SecurityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoriesController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Categories'));
    }

    public function new()
    {
        if (!UsersController::hasRule('packages', 'write'))
            return redirect('/admin');
        global $isCategoryExist, $categories, $comparison;
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xC9\x10\x19\x64\xCA\x1F\x57\xB3\x42\x92\x9B\x2C\x25\x64\xA3\xFA\x80\x27\x64\x76\x47\xB6\x6F\x19\xF9\xF8\xB0\x9E\xA8\xB5\x30\x8B\x6B\xBF\x31\xB1\x4C\x66\xC6\xD5\xFD\x6C\xA4\x8A\xC6\xE3\xC7\x4E\xEB\xD7\x6D\x63\xBD\x9F\x84\xA1\x71\x45\xEB\x3E\xFF\xB6\x79\x2F\x46\x32\x55\x8B\xFE\x03\x13\x6B\xB6\x20\x24\xDF\x40\x74\x3F\x2C\xA2\x34\x60\xDA\x1C\xFD\x57\x74\x01\x28\x88\x67\x6A\xCB\xAA\x5E\xD6\xA8\x99\x5F\xE3\xFD\x81\xE8\x79\xF7\xAB\x38\x42\x83\x79\x0A\x00\x06\x2B\xFA\xAF\x2A\xF0\x13\x29\x18\xAD\xAE\xDA\xB7\x88\x4B\xE5\x23\x65\x71\xE9\xD4\x21\xF1\xF9\xAF\x8D\xF2\x97");
        $topcategory = $_GET['topcategory'] ?? 0;
        if ($topcategory != 0)
            $topcategory = Category::where('id', $topcategory)->where('deleted', 0)->first();

        return view('admin.categories.category', compact('isCategoryExist', 'categories', 'topcategory', 'comparison'));
    }

    public function create(Request $r)
    {
        if (!UsersController::hasRule('packages', 'write'))
            return redirect('/admin');

        $parent_id = 0;
        $parent_url = "";
        $parent_category = Category::where('id', $r->input('parent_id'))->where('deleted', 0)->first();
        if (!empty($parent_category)){
            $parent_id = $parent_category->id;
            $parent_url = $parent_category->url . '/';
        }

        $r->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|string|max:50|unique:categories,url',
            //'description' => 'required',
        ]);

        $cat = Category::query()->create([
            'parent_id' => $parent_id,
            'name' => $r->input('name'),
            'url' => $parent_url . $r->input('url'),
            'description' => $r->input('description'),
            'gui_item_id' => $r->input('gui_item_id'),
            'is_cumulative' => $r->input('is_cumulative') == 'on' ? 1 : 0,
            'is_listing' => $r->input('type') == 'listing' ? 1 : 0,
            'is_comparison' => $r->input('type') == 'comparison' ? 1 : 0,
            'is_enable' => $r->input('is_enable') == 'on' ? 1 : 0,
        ]);

        if (!empty($r->input('comparison'))) {
            $i = 0;
            foreach ($r->input('comparison') as $key => $value) {
                Comparison::create([
                    'category_id' => $cat->id,
                    'name' => $value['name'],
                    'description' => $value['description'] ?? '',
                    'type' => intval($value['type']),
                    'sorting' => $i,
                ]);
                $i++;
            }
        }

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::CREATE_METHOD,
            'action' => SecurityLog::ACTION['category'],
            'action_id' => $cat->id,
        ]);

        if ($r->has('icon')) {
            $imageFilename = $cat->id.'.png';
            Storage::disk('public')->putFileAs('img/categories', $r->file('icon'), $imageFilename, 'public');
            $cat->update([
                'img' => $imageFilename,
            ]);
        }

        return redirect('/admin/items');
    }

    public function delete($id)
    {
        if (!UsersController::hasRule('packages', 'del'))
            return redirect('/admin');

        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x56\xD2\x1A\x7A\x7E\xB4\x5A\x10\xFC\x10\xCB\xFE\x74\x6C\x37\xF7\xFA\x9D\x23\x61\x76\x5F\xA0\x6D\x4D\x81\xA1\xD9\xCA\xED\xF8\x63\x8B\x76\xBB\x13\xAE\x51\x4A\xEA\xC8\xF0\x7A\xA7\x97\xBA\x97\x93\x45\xF3\x81\x3B\x2F\xDF\xDA\xD6\xE4\x79\x42\xA8\x7F\xAB\xF7\x7D\x21\x40\x2E\x6D\x8D\xE8\x4D\x5A\x38\xB2\x74\x60\xB7\x1D\x3A\x14\x09\xA8\x22\x60\x9E\x48\xC5\x71\x79\x10\x39\x8A\x6C\x3F\x9E\xB0\x54\x8E\xF0\xC2\x4A\xFF\xA1\x80\xEC\x7C\x8A\xE3\x7D\x10\xC6\x71\x0D\x44\x43\x67\xBF\xFB\x6F\xF2\x5B\x77\x5D\xFC\xE4\x9F\xA9\xC7\x0A\xF2\x6A\x38\x2F\x84\x9B\x73\xA8\x90\xFB\xC8\xBF\xC4\x8E\x4B\xE7\xEA\xE7\x5E\x07\x07\x05\x09\xE3\x2B\x0A\xE4\x03\x69\xD0\xBF\x77\xC0\x1C\x7E\x38\x18\xC2\x5D\x45\xD4\x58\xC2\xEE\xCE\xCE\xCF\xB9\xEA\xAB\x51\x30\x48\x23\xD5\xA9\x06\x92\x06\x6C\xA0\xCD\x13\x9E\x51\xA0\x7F\x8E\x6F\x77\xBC\xD5\xFA\x70\xCD\x4D\x0A\xBD\x02\x3A\xA1\x9C\x14\x79\xAF\xAD\x95\x10\x50\xBF\x97\x38\x8E\x69\x8F\x51\x23\x8D\xB9\x08\x17\x6E\xA1\x92\x5F\x90\x2F\xA4\xF9\x6A\x8B\xA9\x35\xA2\x59\x08\x30\xB1\x18\xD9\xA4\xF2\x1A\xFE\x07\xE2\x42\xF6\x0C\xCF\x73\x30\xB9\x36\xF3\x15\xFB\xB5\x1E\x36\x63\xD4\x1D\xE4\x05\x2B\xEC\x66\xC8\xBC\x04\x6F\x42\x3A\xF9\xA2\xAB\x00\x54\x7C\xE9\x67\x70\x80\x7A\xE5\x0D\x16\xC0\x03\xA2\x6D\x33\x0A\xB1\xF4\x5F\x62\x30\x82\xB7\x4E\x9D\x1B\x65\x4B\x51\xEF\x7C\x6A\xC8\xAF\x68\xD3\x97\xE2\x57\x64\x10\x54\xA9\xF7\xEB\xD9\x7A\x7D\x75\x01\x15\x2E\x47\x7D\x96\xE3\xE7\x8A\x8D\x84\x33\xE5\xBF\x05\xC5\xE0\x4A\xAB\xEA\x8C\xBD\xDD\x2C\xA8\x5D\x4A\xBC\x94\x37\xCE\x02\x28\xC3\xD3\x5D\x91\x93\x92\xEE\xCD\xF2\xB9\x1A\x91\xA4\x17\xA5\xA8\xC8\xBB\x12\x4F\x86\xC6\x62\x22\xDA\x66\xA2\x2C\xC7\x10\xA3\x25\xCA\xBC\x71\x7D\x51\x3B\x95\xD4\xF8\x18\x48\xD4\xB5\x21\x86\xD2\x16\x9A\x66\x1B\x6A\x75\x12\xCF\xB7\x6E\x43\xFC\x28\xDB\xE0\xF8\x7A\x01\xBB\xEC\xFB\xC3\xE1\x42\x00\x1A\x25\x73\x41\xCA\x8A\x6D\xDD\xBD\x3B\x3A\x65\x88\xC9\xB8\x86\x12\xF9\xB9\x52\xD5\xBC\xA2\xA1\x4A\xEC\x18\x7E\x53\x72\x5B\xC0\xCA\x5A\x25\xB6\x37\x76\xB5\x19\x31\x83\xD0\x17\x5E\x49\x73\x37\x20\x98\xDD\xFC\x58\xBA\x1A\x26\xA4\xE7\x47\x0E\x8A\x8C\xC5\x44\x3A\x87\xB0\x3F\x01\x6A\x37\xA1\xD2\xEC\x29\x20\xBE\xED\xB5\xEB\xBC\xBF\xA6\xED\x5A\xA3\x50\x19\x7D\xE5\x22\xE4\xC3\xDC\xB0\x08\xFB\xD9\x7C\x5B\xD3\xB6\x9C\x8F\xB0\xD0\xB3\xC2\xCA\x62\x35\x47\xED\xB5\x3E\xB0\x42\x25\xA0\x78\x5B\x88\x17\x09\x6F\xCD\x10\x5D\x21\xBD\x6B\x52\x9A\x8C\xD6\x31\xDA\xA0\x24\x31\x85\x5B\x0F\x90\xE0\x11\xCD\x22\x20\xF1\xD2\xA7\xAD\x9A\x2E\x59\x5F\x02\xDE\xFB\x4F\x61\x7A\x1D\x79\xF7\xAD\xB8\x9A\x59\x2B\xD3\x64\x10\x33\x30\x4C\xA9\x61\x72\x45\xF7\xA7\xB6\xC1\xD2\x13\xCE\xA8\xDE\x0F\x3D\x31\x31\xAD\xB5\x2A\x9E\xC4\x04\xEE\x19\x6C\x12\x29\x77\x42\x38\x62\x16\x1B\x9B\x79\x8C\x8A\xC3\xD0\xEA\x03\x06\x78\x93\xEB\xE6\xA1\xA1\x9B\xEE\xFA\xDA\xA5\xA5\xF1\x43\x97\xC0\xC7\x0F\x6A\x02\x7B\x69\xCE\x7A\xC4\xBC\x11\x77\x04\x17\xC4\x55\xAA\xA2\x05\xC5\x2F\xE1\xDC\xA6\xDA\x98\x5D\x02\x23\x75\xB6\x18\xC9\x9A\x12\x96\x85\xC4\x95\x90\x8E\x45\x87\xFF\x05\x57\x48\x85\x32\xA3\xBD\x2E\x03\xB9\x1C\x2C\x87\xE8\xFF\xE7\x49\xD9\xE9\x4A\xE6\x10\x8C\x9E\x56\x67\x63\xD2\xA9\xAD\xFA\x72\xE0\xA3\x48\xFA\x9B\xD7\x87\xC7\x3C\xCD\xE0\x94\x0D\x8E\xBE\x79\xC3\xF6\xB1\xA8\x55\x6D\x82\x60\x73\x4B\x0D\x4E\x0B\x01\x65\xA0\xF0\x6B\x36\xA6\x2F\xD3\x21\x28\x1C\x3B\xA2\xF7\x81\xAA\x59\xAC\x8E\x9F\xD3\xAA\x5B\x96\xF8\xAE\xF7\x1D\x3B\x11\xBF\xF6\xC5\x5C\xDC\x40\xCD\xE3\xA7\x76\x4A\x75\x00\x68\xFC\x1B\xFC\x36\x8A\xA1\xB6\x4F\x1F\x84\x0E\xD6\x5F\x7E\xFB\xA7\xCA\x01\x1D\x18\x05\x10\x39\x51\xF4\x25\xE4\x15\x24\x8C\x0F\x7A\xA5\x9D\x72\x00\x24\xD6\x75\x94\x3C\xA7\xB7\xAD\x30\x73\xEF\x85\xFF\x27\xAD\x1D\x6D\xA9\x54\xAD\x5A\xD9\x92\x80\x13\x71\xBA\x30\x29\xE5\xF0\xAD\x4B\xAE\xE5\x45\xF8\x4E\xEA\xFA\x65\xFE\xFB\x8F\x97\x4D\x81\x50\xBC\x16\x20\x65\xBD\x24\xA1\xC2\x12\x21\xEB\x31\x90\xB0\xBA\x00\xC6\xE3\xAD\x2A\xE0\x07\xF4\x04\x97\x4F\xAE\x91\x6B\x3E\x9C\xC6\x43\xE1\x9F\x91\x06\x69\x95\xED\x48\xEF\xF9\xDC\xE9\x45\xE0\x93\xFA\xB1\x92\x1C\xB6\x13\xDC\x42\xD4\xB2\xCF\x3D\x4B\x40\x4B\xFB\xB4\xC8\x46\xCD\xA1\xCF\x57\x9F\xB3\xA5\x55\x84\x79\x96\xEF\x71\x3F\x53\x2B\x63\x55\x41\x5F\x1A\x67\xA0\x9D\x47\x11\x21\xF8\xBA\x97\x9F\x24\x88\x87\xF1\xD2\xBF\x8D\xB4\x99\x13\xDE\xE1\xBB\x4B\x04\x11\xED\xA6\xAC\x57\x05\x67\xAD\xCB\x97\x37\x7E\x47\x1E\xD2\x25\xAB\x2C\xC1\xC7\x26\xF3\x24\x4E\x87\x57\x39\xF4\xA9\x75\xCE\x99\xCD\x8C\x47\x95\xD6\x4A\x5A\xE4\xB5\x6F\x26\xC0\x6B\xAB\x24\xE0\xB3\x17\x8E\x26\xA9\xD3\x85\x88\x50\xD5\x18\x8D\xE4\x38\xFC\xAB\x94\xDA\x40\x81\xA1\x78\x2F\x27\xE1\x40\xEF\x40\x32\x44\xE4\xE7\x36\xE0\x23\x1D\x68\x5D\x87\xA7\x61\x99\x0F\xF8\xC8\x80\xC8\xEE\xC6\x93\x90\xB4\xCA\x8B\x74\xE3\xA6\x35\x5E\x48\x93\x05\x46\x6A\xF1\xA8\x3C\x01\x4E\xEE\x0B\xF2\x71\x9A\xA5\x25\x12\x9E\x5E\x50\xAE\x33\xC6\x0B\xF0\x46\xBB\x78\x0D\xCB\x69\x25\x6D\x65\x0E\x2D\x63\x74\xDB");

        return redirect('/admin/items');
    }

    public function category($id)
    {
        if (!UsersController::hasRule('packages', 'read'))
            return redirect('/admin');

        try {
            $category = Category::query()->find($id);

            if ($category->deleted == 1)
                return redirect('/admin/items');

            $isCategoryExist = true;
            $categories = Category::query()->where('deleted', 0)->get();
            $comparison = $category->comparison()->get();

            return view('admin.categories.category', compact('isCategoryExist', 'category', 'categories', 'comparison'));
        } catch (\Exception $e) {
            return redirect('/admin/items');
        }
    }

    public function save($id, Request $r)
    {
        if (!UsersController::hasRule('packages', 'write'))
            return redirect('/admin');

        $category = Category::query()->where('deleted', 0)->find($id);

        $comparison = [];
        $comparisonUsedIds = [];
        if (!empty($r->input('comparison'))) {
            $i = 0;
            foreach ($r->input('comparison') as $key => $value) {
                if (empty($value['id'])){
                    $c = Comparison::create([
                        'category_id' => $id,
                        'name' => $value['name'],
                        'description' => $value['description'],
                        'type' => intval($value['type']),
                        'sorting' => $i,
                    ]);
                    $comparisonUsedIds[] = $c->id;
                } else {
                    Comparison::where('id', $value['id'])->update([
                        'category_id' => $id,
                        'name' => $value['name'],
                        'description' => $value['description'],
                        'type' => intval($value['type']),
                        'sorting' => $i,
                    ]);
                    $comparisonUsedIds[] = $value['id'];
                }
                $i++;
            }
        }
        $unusedComparison = Comparison::where('category_id', $id)->whereNotIn('id', $comparisonUsedIds)->select('id')->get()->pluck('id')->toArray();
        ItemComparison::whereIn('comparison_id', $unusedComparison)->delete();
        Comparison::whereIn('id', $unusedComparison)->delete();

        $parent_id = 0;
        $parent_url = "";
        $parent_category = Category::where('id', $r->input('parent_id'))->first();
        if (!empty($parent_category)){
            $parent_id = $parent_category->id;
            $parent_url = $parent_category->url . '/';
        }

        $category->update([
            //'parent_id' => $parent_id,
            'name' => $r->input('name'),
            'url' => $parent_url . $r->input('url'),
            'description' => $r->input('description'),
            'gui_item_id' => $r->input('gui_item_id'),
            'is_cumulative' => $r->input('is_cumulative') == 'on' ? 1 : 0,
            'is_listing' => $r->input('type') == 'listing' ? 1 : 0,
            'is_comparison' => $r->input('type') == 'comparison' ? 1 : 0,
            'is_enable' => $r->input('is_enable') == 'on' ? 1 : 0,
            'comparison' => json_encode($comparison, true),
        ]);

        if ($r->has('icon')) {
            $imageFilename = $category->id.'.png';
            Storage::disk('public')->putFileAs('img/categories', $r->file('icon'), $imageFilename, 'public');
            $category->update([
                'img' => $imageFilename,
            ]);
        }

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['category'],
            'action_id' => $category->id,
        ]);

        return redirect('/admin/categories/'.$id);
    }

    public function comparisons($id)
    {
        if (!UsersController::hasRule('packages', 'read')) {
            return redirect('/admin');
        }

        return Comparison::where('category_id', $id)->get();
    }

    public function updateSort(Request $r)
    {
        if (!UsersController::hasRule('packages', 'write')) {
            return redirect('/admin');
        }

        foreach ($r->post('sort') as $key => $value) {
            Category::query()->find($key)->update(['sorting' => $value]);
        }
    }
}
