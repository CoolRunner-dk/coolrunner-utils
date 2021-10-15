<?php


namespace CoolRunner\Utils\Support\Tools;



use Illuminate\Support\Str;
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
     * @param float|int $n Number for be checked
     * @param float|int $min Lower threshold
     * @param float|int $max Upper threshold
     * @param bool $inclusive Include both lower and upper threshold values
     *
     * @return bool
     */
    public static function between(float|int $n, float|int $min, float|int $max, bool $inclusive = false) : bool
    {
        if ($inclusive) {
            return $min <= $n && $max >= $n;
        }

        return $min < $n && $max > $n;
    }

    /**
     * Caps a numeric value to an upper and lower threshold.
     *
     * @param float|int $value
     * @param float|int $min
     * @param float|int $max
     *
     * @return float|int
     */
    public static function cap(float|int $value, float|int $min, float|int $max) : float|int
    {
        return $value <= $min ? $min : ($value >= $max ? $max : $value);
    }

    /**
     * Caps a numeric value to an upper threshold.
     *
     * @param float|int $value
     * @param float|int $max
     *
     * @return float|int
     */
    public function capUpper(float|int $value, float|int $max) : float|int
    {
        return static::cap($value, PHP_INT_MIN, $max);
    }

    /**
     * Caps a numeric value to a lower threshold.
     *
     * @param float|int $value
     * @param float|int $min
     *
     * @return float|int
     */
    public function capLower(float|int $value, float|int $min) : float|int
    {
        return static::cap($value, $min, PHP_INT_MAX);
    }

    /**
     * Convert a value to a float.
     * Fixes string floats with comma as the decimal point.
     *
     * @param string|int|float $value
     *
     * @return float
     */
    public static function toFloat(string|int|float $value) : float
    {
        if (is_string($value)) {
            $value = str_replace(',', '.', $value);

            $value = preg_replace('/\./', '', $value, substr_count($value, '.') - 1);
        }

        return floatval($value);
    }

    /**
     * Get a fixed length random string of numbers
     *
     * @param int $digits
     * @param null $salt
     *
     * @return string
     * @throws \Exception
     */
    public function randomDigits($digits = 6, $salt = null) : string
    {
        // More entropy - More secure - More better
        $generator = preg_replace('/[^0-9]/', '',
            hash_hmac(
                'sha512',
                (
                    random_bytes($digits) .
                    uniqid('otp-uniq-', true) .
                    env('APP_KEY') .
                    $salt
                ),
                rand(PHP_INT_MIN, PHP_INT_MAX)
            )
        );

        return substr($generator . $generator, rand(0, Str::length($generator)), $digits);
    }
}
