<?php


namespace CoolRunner\Utils\Mixins;

/**
 * Class Str
 *
 * @package CoolRunner\Utils\Mixins
 * @internal
 */
class Str
{
    public function utf8Encode()
    {
        return function ($body) {
            if (!function_exists('mb_convert_encoding')) {
                return $body;
            }

            $encoding = mb_detect_encoding($body);
            if ($encoding) {
                return mb_convert_encoding($body, 'UTF-8', $encoding);
            }

            return utf8_encode($body);
        };
    }

    public function replaceLastOccurrence()
    {
        return function ($search, $replace, $subject)
        {
            $pos = strrpos($subject, $search);
            if ($pos !== false) {
                $subject = substr_replace($subject, $replace, $pos, strlen($search));
            }

            return $subject;
        }

        ;
    }

    public function detectCsvDelimiter()
    {
        return function ($csv_string, array $delimiter_list = null) {
            $delimiter_list = $delimiter_list ?: [",", ";"];
            $delimiters = [];

            foreach ($delimiter_list as $delimiter) {
                $delimiters[$delimiter] = count(explode($delimiter, $csv_string));
            }

            asort($delimiters);

            return \Arr::last(array_keys($delimiters));
        };
    }

    public function isBase64()
    {
        return function (string $str): bool {
            if (base64_encode(base64_decode($str, true)) === $str) {
                return true;
            } else {
                return false;
            }
        };
    }

    public function randomString()
    {

        /**
         * The $characters can be one of following values:
         * - A predefined set (see below)
         * - A custom source string
         * - A callback generating the source string
         *
         * Predefined character sets:
         * - <code>alpha<code> - Letters (both upper and lower case)
         * - <code>num</code> - Numbers
         * - <code>alphanum</code> - Both of the above combined
         *
         *
         * @param int $length
         * @param null|string|Closure $characters Character set
         *
         * @return string A random string of the specified length
         * @author Morten K. Harders 🐢 <mh@coolrunner.dk>
         */
        return function (int $length, $characters = null): string {
            if ($characters === 'num') {
                $characters = '0123456789';
            } elseif ($characters === 'alpha') {
                $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            } elseif ($characters === 'alphanum') {
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            }

            if (!$characters) {
                $characters = function () {
                    $lower = 33;
                    $upper = 126;

                    $str = '';
                    for ($i = $lower; $i <= $upper; $i++) {
                        $str .= mb_chr($i, 'UTF-8');
                    }

                    return $str;
                };
            }

            $characters = value($characters);

            $str = '';
            do {
                $str .= $characters[rand(0, strlen($characters) - 1)];
            } while (strlen($str) < $length);

            return $str;
        };
    }

    public function normalize()
    {
        return function (string $string) : string {
            $replace = \CoolRunner\Utils\Support\Internal\Normalize::loadNormalize();

            return strtr($string, $replace);
        };
    }
}
