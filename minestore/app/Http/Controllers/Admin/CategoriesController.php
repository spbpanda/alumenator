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

        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x56\xD2\x1A\x7A\x7E\xB4\x5A\x10\xFC\x10\xCB\xFE\x74\x6C\x37\xF7\xFA\x9D\x23\x61\x76\x5F\xA0\x6D\x4D\x81\xA1\xD9\xCA\xED\xF8\x63\x8B\x76\xBB\x13\xAE\x51\x4A\xEA\xC8\xF0\x7A\xA7\x97\xBA\x97\x93\x45\xF3\x81\x3B\x2F\xDF\xDA\xD6\xE4\x79\x42\xA8\x7F\xAB\xF7\x7D\x21\x40\x2E\x6D\x8D\xE8\x4D\x5A\x38\xB2\x74\x60\xB7\x1D\x3A\x14\x09\xA8\x22\x60\x9E\x48\xC5\x71\x79\x10\x39\x8A\x6C\x3F\x9E\xB0\x54\x8E\xF0\xC2\x4A\xFF\xA1\x80\xEC\x7C\x8A\xE3\x7D\x10\xC6\x71\x0D\x44\x43\x67\xBF\xFB\x6F\xF2\x5B\x77\x5D\xFC\xE4\x9F\xA9\xC7\x0A\xF2\x6A\x38\x2F\x84\x9B\x73\xA8\x90\xFB\xC8\xBF\xC4\x8E\x4B\xE7\xEA\xE7\x5E\x07\x07\x05\x09\xE3\x2B\x0A\xE4\x03\x69\xD0\xBF\x77\xC0\x1C\x7E\x38\x18\xC2\x5D\x45\xD4\x58\xC2\xEE\xCE\xCE\xCF\xB9\xEA\xAB\x51\x30\x48\x23\xD5\xA9\x06\x92\x06\x6C\xA0\xCD\x13\x9E\x51\xA0\x7F\x8E\x6F\x77\xBC\xD5\xFA\x70\xCD\x4D\x0A\xBD\x02\x3A\xA1\x9C\x14\x79\xAF\xAD\x95\x10\x50\xBF\x97\x38\x8E\x69\x8F\x51\x23\x8D\xB9\x08\x17\x6E\xA1\x92\x5F\x90\x2F\xA4\xF9\x6A\x8B\xA9\x35\xA2\x59\x08\x30\xB1\x18\xD9\xA4\xF2\x1A\xFE\x07\xE2\x42\xF6\x0C\xCF\x73\x30\xB9\x36\xF3\x15\xFB\xB5\x1E\x36\x63\xD4\x1D\xE4\x05\x2B\xEC\x66\xC8\xBC\x04\x6F\x42\x3A\xF9\xA2\xAB\x00\x54\x7C\xE9\x67\x70\x80\x7A\xE5\x0D\x16\xC0\x03\xA2\x6D\x33\x0A\xB1\xF4\x5F\x62\x30\x82\xB7\x4E\x9D\x1B\x65\x4B\x51\xEF\x7C\x6A\xC8\xAF\x68\xD3\x97\xE2\x57\x64\x10\x31\xBA\xE6\xC3\xF1\x72\x76\x62\x14\x46\x4F\x24\x5D\x92\xF6\xDC\xA8\x90\x99\x6C\xB3\xBD\x2C\xE3\xF3\x47\xE6\xA4\xCE\xF1\xC2\x7F\xFC\x5F\x0B\xF6\x99\x7B\xC3\x46\x72\x9F\x98\x33\xD0\xC7\xD7\xE6\xB6\xD8\xB9\x1A\x91\xA4\x17\xA5\xAC\x8B\xFA\x46\x0A\xC1\x89\x30\x7B\xD7\x7F\xB9\x3D\xCE\x14\xF0\x60\xDF\xD9\x5B\x7A\x2A\x5F\xF0\xB8\x9D\x6C\x2D\xB0\xC8\x21\x81\xD2\x18\x9A\x07\x4C\x74\x48\x32\xC5\xF4\x2B\x12\xB1\x54\xBF\xDA\xC8\x5A\x2A\x91\xCA\xC7\xBD\x86\x13\x52\x5F\x79\x2A\x28\x93\xD7\x0F\xF6\xBB\x31\x3B\x21\xDF\xFC\x9F\xCE\x4B\xA2\xB0\x5C\xCA\xA0\xB4\x91\x1B\xB9\x5D\x2C\x0A\x7A\x52\xCD\xD4\x1C\x6C\xF8\x73\x7E\xB1\x50\x72\xDF\x8F\x45\x17\x08\x23\x6C\x2C\xB5\x99\xB9\x14\xFF\x4E\x63\xE0\xEA\x40\x0E\x84\x8C\xC1\x0D\x7E\x8B\x9D\x6A\x53\x26\x30\xA1\xCF\xF2\x29\x27\xFA\xA8\xF9\xAE\xE8\xFA\xE2\xE7\x19\xE6\x12\x5C\x2D\xE9\x22\xEF\xE9\xC1\xAE\x08\xEA\xD5\x56\x5B\xD3\xB6\x9C\x8F\xB0\xD0\xB3\xC2\xCA\x65\x71\x7F\xA8\xEB\x40\xF5\x06\x22\xA0\x65\x45\x88\x06\x05\x45\xCD\x10\x3C\x71\xED\x17\x3F\xD5\xC8\x93\x7D\x89\xA1\x4E\x65\xE2\x21\x4E\xC2\xA9\x42\x82\x6C\x3A\xEB\x85\xEF\xE8\xC8\x0A\x01\x08\x3D\xF2\xE0\x4E\x63\x79\x1C\x5C\xEB\xAB\xB1\xCD\x14\x79\x9E\x7E\x1B\x74\x27\x48\xBA\x6C\x7B\x52\xE6\xEA\xB9\x8B\x88\x6D\xA1\xEF\x91\x5D\x64\x4E\x78\xE9\xB2\x26\x9E\xC0\x4D\xCB\x40\x31\x70\x00\x7D\x4A\x38\x7A\x00\x6F\xE1\x27\xE5\xF5\x91\x99\xBE\x5A\x6A\x37\xD4\xF1\xFC\xE2\xF3\xDE\xCE\xFE\xCF\xD1\xB3\x94\x07\xD2\x8C\x94\x73\x19\x47\x38\x3C\x9C\x33\x90\xE5\x7D\x38\x43\x0A\x9F\x52\xB5\xAE\x0A\xEE\x23\xAD\xA0\x8C\xC7\x86\x5D\x7E\x42\x20\xE2\x50\xD3\x80\x55\xC3\xC4\x96\xD1\x98\x8E\x45\x87\xFF\x05\x57\x64\xCB\x7F\xA9\xA3\x66\x4E\xFC\x32\x45\xDB\xB1\xA9\xB4\x17\x92\x96\x0B\xB4\x54\x84\x99\x17\x23\x2E\x9B\xE7\xFE\xFD\x7B\xED\xBD\x1D\xAE\x93\xC0\xDB\x86\x7E\x97\xAE\xD0\x1C\xBA\xBE\x18\x93\xA6\xCD\xC5\x1A\x29\xC7\x2C\x20\x37\x7E\x0B\x48\x54\x30\xA4\xE1\x66\x12\xA6\x2C\xCE\x3B\x51\x67\x57\xA6\xD3\xB4\xA9\x59\x86\xBE\xB2\xF0\x9D\x2B\xCF\xBD\xED\xA2\x4F\x72\x45\xE6\x9A\x8A\x1B\xC6\x5A\xA9\x86\xCB\x13\x39\x51\x3C\x51\xD0\x20\xDA\x5E\xEE\xB0\x82\x4F\x7E\xD4\x5E\xAA\x32\x31\xBF\xE2\x86\x52\x61\x6B\x40\x53\x6C\x04\xFC\x32\xE9\x30\x24\x85\x12\x60\xD9\xE0\x06\x08\x1B\xE8\x72\xFE\x30\xA2\xA6\xA4\x24\x40\xCE\x99\xBB\x0F\xF3\x7E\x39\xF0\x38\xE2\x1D\xC3\x88\xE1\x70\x05\xD3\x5F\x47\x9E\xF7\xEE\x0D\xBB\xE3\x56\xFE\x53\xFD\x82\x51\xB6\xD6\x8F\x8A\x53\x81\x54\xF5\x52\x2C\x4F\xBD\x24\xA1\xC2\x12\x21\xEB\x36\xD1\xF3\xEE\x49\xF4\xA4\xC9\x49\xA4\x00\xF4\x19\x89\x4F\xAA\xD8\x72\x32\xF5\x87\x17\xA2\xD7\x91\x0E\x15\xF0\xB5\x0B\xAA\xA9\xF5\xA9\x11\x84\x93\xFE\xF4\x9B\x1C\xED\x39\xDC\x1F\xD4\xF1\x8E\x69\x08\x08\x16\xD9\xC8\xAD\x1E\x8E\xE4\x9F\x03\xD6");

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
