<?php
/**
 * @package coolrunner-utils
 * @copyright 2021
 */

namespace CoolRunner\Utils\Support\Tools;

use CoolRunner\Utils\Interfaces\Providers\ProvidesCoordinates;

class Coords
{
    const LAT_MAX = 90;
    const LAT_MIN = -90;
    const LON_MAX = 180;
    const LON_MIN = -180;

    /**
     * Distance in meters between coordinates
     *
     * @param float $from_latitude
     * @param float $from_longitude
     * @param float $to_latitude
     * @param float $to_longitude
     *
     * @return float|int
     */
    public static function distanceBetween(float $from_latitude, float $from_longitude, float $to_latitude, float $to_longitude) : float|int
    {
        $lat_from = deg2rad($from_latitude);
        $lon_from = deg2rad($from_longitude);
        $lat_to   = deg2rad($to_latitude);
        $lon_to   = deg2rad($to_longitude);

        $lat_delta = $lat_to - $lat_from;
        $lon_delta = $lon_to - $lon_from;

        $earth_radius = 6371000;

        $angle = 2 * asin(sqrt(pow(sin($lat_delta / 2), 2) +
                               cos($lat_from) * cos($lat_to) * pow(sin($lon_delta / 2), 2)));

        return $angle * $earth_radius;
    }

    /**
     * Distance in meters between two entities
     *
     * @param ProvidesCoordinates $from
     * @param ProvidesCoordinates $to
     *
     * @return float|int
     */
    public static function distanceBetweenEntities(ProvidesCoordinates $from, ProvidesCoordinates $to) : float|int
    {
        return static::distanceBetween(
            $from->getLatitude(),
            $from->getLongitude(),
            $to->getLatitude(),
            $to->getLongitude()
        );
    }

    public static function round(float $measurement, int $precision = 6) : float
    {
        return round($measurement, $precision);
    }
}