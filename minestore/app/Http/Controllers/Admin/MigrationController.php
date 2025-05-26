<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\Tebex\MarkMigrationJob;
use App\Jobs\Tebex\MigrateCategoriesJob;
use App\Jobs\Tebex\MigratePackagesJob;
use App\Jobs\Tebex\MigratePaymentsJob;
use App\Models\PlatformMigration;
use Bus;
use Illuminate\Http\Request;
use Log;

class MigrationController extends Controller
{
    public function index()
    {
        $migrations = PlatformMigration::all();

        return view('admin.migrations.index', compact('migrations'));
    }

    public function create()
    {
        $platforms = [
            'Tebex' => 'tebex',
        ];

        return view('admin.migrations.create', compact('platforms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'platform_name' => 'required|string|max:255',
            'platform_token' => 'required|string|max:255',
            'platform_key' => 'nullable|string|max:255',
            'migrate_payments' => 'string',
        ]);

        $migratePayments = filter_var($request->migrate_payments, FILTER_VALIDATE_BOOLEAN);
        if ($migratePayments && !$request->platform_key) {
            return redirect()->back()->with('warning', 'Platform key is required for payment migration.');
        }

        $platformMigration = PlatformMigration::create([
            'platform_name' => $request->platform_name,
            'platform_token' => $request->platform_token,
            'platform_key' => $request->platform_key ?? null,
            'status' => PlatformMigration::STATUS_CREATED,
        ]);

        $platform = mb_strtolower($request->platform_name);
        switch ($platform) {
            case 'tebex':
                if ($platformMigration->status !== PlatformMigration::STATUS_CREATED) {
                    Log::warning('Migration is not in pending state', ['migration_id' => $platformMigration->id]);
                    return redirect()->back()->with('warning', 'Migration is not in pending state.');
                }

                $busData = [
                    new MarkMigrationJob(PlatformMigration::STATUS_PENDING, $platformMigration),
                    new MigrateCategoriesJob($platformMigration->platform_token, $platformMigration->id),
                    new MigratePackagesJob($platformMigration->platform_token, $platformMigration->id),
                ];

                if ($migratePayments && $request->platform_key) {
                    $busData[] = new MigratePaymentsJob($platformMigration->platform_key, $platformMigration->id);
                }

                $busData[] = new MarkMigrationJob(PlatformMigration::STATUS_COMPLETED, $platformMigration);

                Bus::chain($busData)->catch(function ($exception) use ($platformMigration) {
                    Log::error('Migration failed: ' . $exception->getMessage(), [
                        'migration_id' => $platformMigration->id,
                        'exception' => $exception,
                    ]);

                    try {
                        $platformMigration->update([
                            'status' => PlatformMigration::STATUS_FAILED,
                        ]);
                    } catch (\Exception $updateException) {
                        Log::critical('Failed to update migration status: ' . $updateException->getMessage(), [
                            'migration_id' => $platformMigration->id,
                        ]);
                    }
                })->dispatch();

                $platformMigration->update([
                    'platform_key' => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
                ]);
                break;
            default:
                return redirect()->route('migrations.index')->with('error', 'Unsupported platform.');
        }

        return redirect()->route('migrations.index')->with('success', 'Platform migration created successfully.');
    }
}
