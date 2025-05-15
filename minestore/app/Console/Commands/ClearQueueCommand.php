<?php

namespace App\Console\Commands;

use App\Models\CmdQueue;
use Illuminate\Console\Command;

class ClearQueueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minestore:clear-queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear Commands Queue table.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing Commands Queue table...');

        CmdQueue::truncate();

        $this->info('Commands Queue table cleared.');
    }
}
