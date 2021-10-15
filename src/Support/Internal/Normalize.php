<?php
/**
 * @package coolrunner-utils
 * @copyright 2021
 */

namespace CoolRunner\Utils\Support\Internal;

/**
 * @internal
 */
class Normalize
{
    protected static ?array $normalize = null;


    public static function loadNormalize() : array
    {
        if (!static::$normalize) {
            $file     = __DIR__ . '/../../resource/normalize.json';
            $contents = file_get_contents($file);

            static::$normalize = json_decode($contents, true);
        }

        return static::$normalize;
    }
}