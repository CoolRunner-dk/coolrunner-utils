<?php


namespace CoolRunner\Utils\Support\Tools;



use Illuminate\Support\Traits\Macroable;

class Number
{
    use Macroable;


    protected static function getFacadeAccessor(): string
    {
        return "Number";
    }

    /**
     * Number is between two values.
     *
     * @param int|float|float $n Number for be checked
     * @param int|float|float $min Lower threshold
     * @param int|float|float $max Upper threshhold
     * @param bool $inclusive Include both lower and upper threshold values
     *
     * @return bool
     * @author Morten K. Harders 🐢 <mh@coolrunner.dk>
     */
    public static function between($n, $min, $max, $inclusive = false)
    {
        if ($inclusive) {
            return $min <= $n && $max >= $n;
        }

        return $min < $n && $max > $n;
    }

    /**
     * Caps a numeric value to a upper and lower threshold.
     *
     * @param float|float|int $value
     * @param float|float|int $min
     * @param float|float|int $max
     *
     * @return int|string
     * @throws \InvalidArgumentException if any of the parameters isn't numeric
     * @author Morten K. Harders 🐢 <mh@coolrunner.dk>
     */
    public static function minmax($value, $min, $max)
    {
        if (!is_numeric($value) || !is_numeric($min) || !is_numeric($max)) {
            throw new \InvalidArgumentException('Arguments for minmax must be of a numeric type');
        }

        return $value <= $min ? $min : ($value >= $max ? $max : $value);
    }

    public static function toFloat($value) {
        if(is_string($value)) {
            $value = str_replace(',','.',$value);

            $value = preg_replace('/\./','',$value,substr_count($value,'.') - 1);
        }
        return floatval($value);
    }
}
