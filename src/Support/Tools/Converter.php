<?php
/**
 * @package coolrunner-utils
 * @copyright 2021
 */

namespace CoolRunner\Utils\Support\Tools;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;

class Converter
{
    protected static array $multipliers = [
        'volume'   => [
            // Metric
            'mm3'   => 1,
            'cm3'   => 1000,
            'dm3'   => 1000000,
            'm3'    => 1000000000,
            'ml'    => 1000,
            'cl'    => 10000,
            'dl'    => 100000,
            'l'     => 1000000,
            // Imperial
            'cu in' => 16387.064,
            'cu ft' => 28316846.6,
            'cu yd' => 764554857.98,
            'c'     => 236588.236,
            'pt'    => 473176.473,
            'qt'    => 946352.946,
            'gal'   => 3785411.78,
        ],
        'weight'   => [
            // Metric
            'g'   => 1,
            'kg'  => 1000,
            't'   => 1000000,
            // Imperial
            'oz'  => 28.3495231,
            'lbs' => 453.59237,
            'st'  => 6350.29318,
        ],
        'distance' => [
            // Metric
            'mm' => 1,
            'cm' => 10,
            'm'  => 1000,
            'km' => 1000000,
            // Imperial
            'in' => 25.4,
            'ft' => 304.8,
            'yd' => 914.4,
            'mi' => 1609344,
        ],
    ];

    public static function volume($value, string $from, string $to, int $precision = 3) : float
    {
        $from_mul = static::$multipliers['volume'][$from] ?? null;
        $to_mul   = static::$multipliers['volume'][$to] ?? null;

        $mul = $from_mul / $to_mul;

        return round($value * $mul, $precision);
    }

    public static function weight($value, string $from, string $to, int $precision = 3) : float
    {
        $from_mul = static::$multipliers['weight'][$from] ?? null;
        $to_mul   = static::$multipliers['weight'][$to] ?? null;

        $mul = $from_mul / $to_mul;

        return round($value * $mul, $precision);
    }

    public static function distance($value, string $from, string $to, int $precision = 3) : float
    {
        $from_mul = static::$multipliers['distance'][$from] ?? null;
        $to_mul   = static::$multipliers['distance'][$to] ?? null;

        $mul = $from_mul / $to_mul;

        return round($value * $mul, $precision);
    }

    /**
     * @param Builder|QueryBuilder|Relation $query
     *
     * @return string
     * @throws \Exception
     */
    public static function queryToString(Relation|Builder|QueryBuilder $query) : mixed
    {
        if (env('APP_ENV') === 'production') {
            throw new \Exception('builder_get_raw_query should not be used in production!');
        }

        /** @var Builder $query */
        $sql      = $query->toSql();
        $bindings = $query->getBindings();

        return static::queryPartsToString($sql, $bindings);
    }

    public static function queryPartsToString($sql, $bindings, $no_quote = false)
    {
        if (env('APP_ENV') === 'production') {
            throw new \Exception('parts_get_raw_query should not be used in production!');
        }

        $sql = Str::of($sql);

        foreach ($bindings as $binding) {
            if (!preg_match('/^[0-9]$/', $binding)) {
                $binding = $no_quote ? $binding : "'$binding'";
            }
            $sql = $sql->replaceFirst('?', $binding);
        }

        return (string)$sql;
    }
}