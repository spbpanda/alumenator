<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\SettingsController;
use App\PaymentLibs\MinecraftColorParser as MinecraftColors;

class MinecraftColorParser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minecraft:color {text}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert Minecraft color codes to HTML';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $text = $this->argument('text');
        $converter = new MinecraftColors();

        $output = $converter->convertToHtml($text, true);

        $this->info($output);
    }
}
