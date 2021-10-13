<?php
namespace CoolRunner\Utils\Models\Audit;

use CoolRunner\Utils\Models\Logging\ClientLog;
use CoolRunner\Utils\Models\Logging\InputLog;
use CoolRunner\Utils\Traits\Models\HasSessionUuid;
use CoolRunner\Utils\Traits\Models\SavesEnvironment;
use CoolRunner\Utils\Traits\Models\SavesModelAsync;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Audit extends \OwenIt\Auditing\Models\Audit
{
   use SavesModelAsync;
   use SavesEnvironment;
   use HasSessionUuid;

    public function inputLogs() : HasMany|InputLog {
        return $this->hasMany(InputLog::class,'session_uuid','session_uuid');
    }

    public function clientLogs() : HasMany|ClientLog {
        return $this->hasMany(ClientLog::class,'session_uuid','session_uuid');
    }
}
