<?php

namespace CoolRunner\Utils\Support\Internal;

use Ramsey\Uuid\Uuid;

/**
 * Class SessionUuid
 *
 * @package Coolrunner\Support\Tools
 *
 * @internal
 */
class SessionUuid
{
    protected static ?string $session_id = null;

    public static function get()
    {
        return static::$session_id = static::$session_id ?: Uuid::uuid4();
    }
}
