<?php
/**
 * @package coolrunner-utils
 * @copyright 2021
 */

namespace CoolRunner\Utils\Interfaces\Providers;

interface ProvidesCoordinates
{
    public function getLatitude() : float;

    public function getLongitude() : float;
}