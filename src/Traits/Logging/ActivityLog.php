<?php


namespace CoolRunner\Utils\Traits\Logging;


use CoolRunner\Utils\Http\Middleware\AuditModelsChanges;
use OwenIt\Auditing\Auditable;

trait ActivityLog
{
    use Auditable;

    public static function bootActivityLog() {
        self::$auditingDisabled = true;

        if(AuditModelsChanges::$auditing_enabled)
            self::$auditingDisabled = false;
    }
}
