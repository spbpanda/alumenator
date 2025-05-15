<?php

namespace App\Console\Commands;

use App\Models\CommandHistory;
use Illuminate\Console\Command;

class ClearCommandHistoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minestore:clear-command-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear Commands History table.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing command history table...');

        CommandHistory::query()->delete();

        $this->info('Command history table cleared.');
    }
}
