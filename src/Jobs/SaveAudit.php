<?php
namespace CoolRunner\Utils\Jobs;

use App\Models\Tools\ClientLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class SaveAudit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected \CoolRunner\Utils\Models\Audit\Audit $audit) {}

    public function handle() {
        $this->audit->save(['force' => true]);
    }
}
