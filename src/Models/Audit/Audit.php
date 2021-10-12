<?php
namespace CoolRunner\Utils\Models\Audit;

use CoolRunner\Utils\Traits\Models\HasSessionUuid;
use CoolRunner\Utils\Traits\Models\SavesEnvironment;
use CoolRunner\Utils\Traits\Models\SavesModelAsync;

class Audit extends \OwenIt\Auditing\Models\Audit
{
   use SavesModelAsync;
   use SavesEnvironment;
   use HasSessionUuid;
}
