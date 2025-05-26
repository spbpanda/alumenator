<?php

namespace App\Jobs\PayNow\Variables;

use App\Integrations\PayNow\Management;
use App\Observers\VariableObserver;
use App\Models\Variable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessVariableDeletion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Variable $variable;

    public function __construct(Variable $variable)
    {
        $this->variable = $variable;
        $this->onQueue('paynow');
    }

    public function handle(): void
    {
        $observer = app(VariableObserver::class);
        $management = app(Management::class);
        $observer->deleteVariable($this->variable, $management);
    }
}
