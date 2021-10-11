<?php
namespace CoolRunner\Utils\Models\Audit;


use CoolRunner\Utils\Jobs\SaveAudit;

class Audit extends \OwenIt\Auditing\Models\Audit
{
    public function save(array $options = [])
    {
        if(isset($options['force'])) {
            return parent::save();
        }

        $this->service = env('APP_NAME');

        SaveAudit::dispatch($this);

        return true;
    }
}
