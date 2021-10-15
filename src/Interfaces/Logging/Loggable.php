<?php
/**
 * @package coolrunner-utils
 * @copyright 2021
 */

namespace CoolRunner\Utils\Interfaces\Logging;

use CoolRunner\Utils\Models\Logging\InputLog;

interface Loggable
{
    public function log(InputLog $log);
}