<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatronsRequest;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class PatronsController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Patrons'));
    }

    public function index()
    {
        if (!UsersController::hasRule('cms', 'read')) {
            return redirect('/admin');
        }

        $settings = Setting::select('patrons_enabled', 'patrons_groups', 'patrons_description')->first();

        $patronsGroups = [];
        if (!empty($settings->patrons_groups)) {
            $decoded = json_decode($settings->patrons_groups, true);
            $patronsGroups = is_array($decoded) ? $decoded : [];
        }

        if ($patronsGroups && isset($patronsGroups[0]) && is_array($patronsGroups[0]) && isset($patronsGroups[0]['value'])) {
            $patronsGroups = array_map(fn($group) => (int)$group['value'], $patronsGroups);
        }

        $patronsGroupsString = implode(', ', $patronsGroups);

        $settings->patrons_description = $settings->patrons_description
            ? html_entity_decode($settings->patrons_description, ENT_QUOTES, 'UTF-8')
            : '';

        return view('admin.patrons.index', compact('settings', 'patronsGroups', 'patronsGroupsString'));
    }

    public function store(StorePatronsRequest $request)
    {
        if (!UsersController::hasRule('cms', 'write')) {
            return redirect('/admin');
        }

        $patronsEnabled = $request->has('patrons_enabled');
        $patronsDescription = $request->input('patrons_description');
        $patronsGroups = $request->input('patrons_groups');

        $decodedGroups = json_decode($patronsGroups, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Invalid JSON for patrons_groups: ' . json_last_error_msg());
            return redirect()->back()->with('error', __('Invalid JSON for patrons groups.'));
        }

        if (!is_array($decodedGroups)) {
            Log::error('patrons_groups is not an array: ' . var_export($decodedGroups, true));
            return redirect()->back()->with('error', __('Patrons Groups must be a valid array.'));
        }

        $groupValues = [];
        foreach ($decodedGroups as $group) {
            if (!isset($group['value']) || !is_numeric($group['value']) || !preg_match('/^\d+$/', $group['value'])) {
                Log::error('Invalid value for patrons_groups: ' . ($group['value'] ?? 'undefined'));
                return redirect()->back()->with('error', __('Patrons Groups must be numeric.'));
            }
            $groupValues[] = (int)$group['value'];
        }

        Setting::updateOrCreate([], [
            'patrons_enabled'     => $patronsEnabled,
            'patrons_description' => htmlspecialchars($patronsDescription, ENT_QUOTES, 'UTF-8'),
            'patrons_groups'      => json_encode($groupValues),
        ]);

        return redirect()->back()->with('success', __('Patrons Settings updated successfully!'));
    }
}
