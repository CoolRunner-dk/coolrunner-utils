<?php
/**
 * @package route-runner
 * @copyright 2021
 */

namespace CoolRunner\Utils\Interfaces\Logging;

interface AddsContextToSlack
{
    public function addContext(array $context) : array;
}