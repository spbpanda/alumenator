<?php

namespace App\Console\Commands;

use App\Models\Theme;
use Illuminate\Console\Command;

class FixThemeVersion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minestore:fix-theme';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix the theme version to 3.4.5';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing theme version...');
        $theme = Theme::where('name', 'Default Theme')->first();
        if ($theme) {
            $this->info('Found theme: ' . $theme->name);
            $theme->update([
                'version' => '3.4.5',
            ]);

            $this->info('Theme version updated to 3.4.5');
        }

        $this->info('Theme version fix completed.');
    }
}
