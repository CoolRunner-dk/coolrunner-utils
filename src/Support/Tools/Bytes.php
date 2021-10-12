<?php

namespace CoolRunner\Utils\Support\Tools;


class Bytes
{
    public static function in(int $bytes, string $abbreviation, int $precision = 2, $as_float = false) : float|string
    {
        $dividers = [
            'kb' => 1024,
            'mb' => 1048576,
            'gb' => 1073741824,
            'tb' => 1099511627776,
        ];

        $divider = $dividers[$abbreviation] ?? false;

        if (!$divider) {
            throw new \Exception("Unknown abbreviation for byte conversion: $abbreviation");
        }

        if (!$as_float) {
            return number_format($bytes / $divider, $precision) . " $abbreviation";
        }

        return round($bytes / $divider, $precision);
    }

    public static function reduce(int $bytes, int $precision = 2) : string
    {
        $dividers = [
            'kb',
            'mb',
            'gb',
            'tb',
        ];

        $value        = $bytes;
        $abbreviation = 'b';
        foreach ($dividers as $divider) {
            if ($value > 1024) {
                $abbreviation = $divider;
                $value        /= 1024;
            } else {
                break;
            }
        }

        $value = number_format($value, $precision);

        return "$value $abbreviation";
    }
}
