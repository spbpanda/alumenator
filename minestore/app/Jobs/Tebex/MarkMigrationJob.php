<?php

namespace App\Jobs\Tebex;

use App\Models\PlatformMigration;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MarkMigrationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $status;
    protected PlatformMigration $migration;

    /**
     * Create a new job instance.
     */
    public function __construct($status, PlatformMigration $migration)
    {
        $this->status = $status;
        $this->migration = $migration;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        switch ($this->status) {
            case PlatformMigration::STATUS_PENDING:
                $this->migration->update([
                    'status' => PlatformMigration::STATUS_PENDING,
                ]);
                break;
            case PlatformMigration::STATUS_COMPLETED:
                $this->migration->update([
                    'status' => PlatformMigration::STATUS_COMPLETED,
                ]);
                break;
            default:
                $this->migration->update([
                    'status' => PlatformMigration::STATUS_FAILED,
                ]);
        }
    }
}
