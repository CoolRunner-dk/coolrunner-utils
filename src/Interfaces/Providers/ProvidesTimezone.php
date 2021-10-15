<?php
/**
 * @package coolrunner-utils
 * @copyright 2021
 */

namespace CoolRunner\Utils\Interfaces\Providers;

interface ProvidesTimezone
{
    public function getTimezone() : string;
}