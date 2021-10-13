<?php


namespace CoolRunner\Utils\Mixins;

/**
 * Class Dates
 *
 * @package CoolRunner\Utils\Mixins
 * @internal
 */
class Dates
{
    public function lastBusinessDay()
    {
        return function () {
            if (in_array(date('D'), ['Sun', 'Mon'])) {
                return new \Carbon\Carbon(strtotime('last friday'));
            } else {
                return new \Carbon\Carbon(strtotime('yesterday'));
            }
        };
    }
}
