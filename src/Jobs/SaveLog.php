<?php
namespace CoolRunner\Utils\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class SaveLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $log;

    public function __construct($log)
    {
        $this->log = $log;
    }

    public function handle()
    {
        $this->log->save();
    }
}