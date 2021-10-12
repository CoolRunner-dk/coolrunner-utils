<?php
namespace CoolRunner\Utils\Traits\Models;

use CoolRunner\Utils\Jobs\SaveModel;

/**
 * Trait SaveModelAsync
 */
trait SavesModelAsync
{
    public function save(array $options = [])
    {
        if(isset($options['force'])) {
            return parent::save();
        }
        
        $job = new SaveModel($this);

//        $job->onQueue(config(''));
//        $job->onConnection('');

        dispatch($job);

        return true;
    }
}
