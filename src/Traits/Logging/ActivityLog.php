<?php


namespace CoolRunner\Utils\Traits\Logging;


use CoolRunner\Utils\Http\Middleware\AuditModelsChanges;
use Illuminate\Support\Facades\Config;
use OwenIt\Auditing\Auditable;

trait ActivityLog
{
    use Auditable;

    public static function bootActivityLog() {
        static::$auditingDisabled = true;

        if(AuditModelsChanges::isEnabled())
            static::$auditingDisabled = false;
    }

    protected function initializeActivityLog() {

        $this->auditDriver  = [
            'table'      => 'audits',
            'connection' => 'logging',
        ];
    }
}
