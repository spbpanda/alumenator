<?php

namespace App\Jobs\PayNow\Variables;

use App\Observers\VariableObserver;
use App\Models\Variable;
use App\Integrations\PayNow\Management;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessVariableUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Variable $variable;
    protected array $data;
    protected Management $management;

    public function __construct(Variable $variable, array $data, Management $management)
    {
        $this->variable = $variable;
        $this->data = $data;
        $this->management = $management;
        $this->onQueue('paynow');
    }

    public function handle(): void
    {
        $observer = app(VariableObserver::class);
        $observer->updateVariable($this->variable, $this->data, $this->management);
    }
}
