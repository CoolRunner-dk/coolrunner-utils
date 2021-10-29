<?php
/**
 * @package coolrunner-utils
 * @copyright 2021
 */

namespace CoolRunner\Utils\Support\Tools;

use Illuminate\Support\Collection;

class Cast
{
    public static function nullToEmptyStrings(mixed $data)
    {
        if (is_object($data) && !$data instanceof Collection) {
            return $data;
        }

        if (is_array($data)) {
            $data = array_map([static::class, 'nullToEmptyStrings'], $data);
        } elseif ($data instanceof Collection) {
            $data = $data->map([static::class, 'nullToEmptyStrings']);
        } elseif (is_null($data)) {
            $data = '';
        }

        return $data;
    }
}