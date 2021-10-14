<?php
namespace CoolRunner\Utils\Traits\Models;

use CoolRunner\Utils\Jobs\SaveModel;

/**
 * Trait SaveModelAsync
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait SavesModelAsync
{
    public function save(array $options = [])
    {
        if(isset($options['force'])) {
            return parent::save();
        }

        $job = new SaveModel($this);

        if($queue = config('utils.jobs.save_model.queue'))
            $job->onQueue($queue);

        if($connection = config('utils.jobs.save_model.connection'))
            $job->onConnection($connection);

        dispatch($job);

        return true;
    }
}
