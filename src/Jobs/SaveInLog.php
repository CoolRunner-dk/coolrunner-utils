<?php
namespace CoolRunner\Utils\Jobs;

use CoolRunner\Utils\Models\InLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class SaveInLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $log;

    public function __construct($log)
    {
        $this->log = $log;
    }

    public function handle()
    {

        /*  InLog */
        $log = InLog::where('uuid', $this->log->uuid)->where('created_at',$this->log->created_at)->first();

        if($log){
            $log->mergeInputs($log,$this->log);
            $log->save();
        }
        else{
            $this->log->save();
        }
    }
}