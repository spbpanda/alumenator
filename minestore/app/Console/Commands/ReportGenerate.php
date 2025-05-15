<?php

namespace App\Console\Commands;

use App\Events\MonthlyReportGenerated;
use App\Helpers\Statistics\MainHelper;
use Illuminate\Console\Command;

class ReportGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate month report and send notification';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $revenue = (new MainHelper)->getRevenueByMonth(now()->subMonth()->month);
        $month = now()->subDay()->monthName;
        event(new MonthlyReportGenerated($month, $revenue));

        return Command::SUCCESS;
    }
}
