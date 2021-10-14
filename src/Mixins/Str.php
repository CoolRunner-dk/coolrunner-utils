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

    public function randomNumber()
    {
        return function ($digits = 6, $salt = null) {
            // More entropy - More secure - More better
            $generator = preg_replace('/[^0-9]/', '',
                hash_hmac(
                    'sha512',
                    (
                        rand(PHP_INT_MIN, PHP_INT_MAX) .
                        uniqid('otp-uniq-', true) .
                        env('APP_KEY') .
                        $salt
                    ),
                    rand(PHP_INT_MIN, PHP_INT_MAX)
                )
            );
            return substr($generator . $generator, rand(0, \Str::length($generator)), $digits);
        };
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
        return function (string $string): string {
            $replace = array(
                'ъ' => '-',
                'Ь' => '-',
                'Ъ' => '-',
                'ь' => '-',
                'Ă' => 'A',
                'Ą' => 'A',
                'À' => 'A',
                'Ã' => 'A',
                'Á' => 'A',
                'Æ' => 'A',
                'Â' => 'A',
                'Å' => 'A',
                'Ä' => 'Ae',
                'Þ' => 'B',
                'Ć' => 'C',
                'ץ' => 'C',
                'Ç' => 'C',
                'È' => 'E',
                'Ę' => 'E',
                'É' => 'E',
                'Ë' => 'E',
                'Ê' => 'E',
                'Ğ' => 'G',
                'İ' => 'I',
                'Ï' => 'I',
                'Î' => 'I',
                'Í' => 'I',
                'Ì' => 'I',
                'Ł' => 'L',
                'Ñ' => 'N',
                'Ń' => 'N',
                'Ø' => 'O',
                'Ó' => 'O',
                'Ò' => 'O',
                'Ô' => 'O',
                'Õ' => 'O',
                'Ö' => 'Oe',
                'Ş' => 'S',
                'Ś' => 'S',
                'Ș' => 'S',
                'Š' => 'S',
                'Ț' => 'T',
                'Ù' => 'U',
                'Û' => 'U',
                'Ú' => 'U',
                'Ü' => 'Ue',
                'Ý' => 'Y',
                'Ź' => 'Z',
                'Ž' => 'Z',
                'Ż' => 'Z',
                'â' => 'a',
                'ǎ' => 'a',
                'ą' => 'a',
                'á' => 'a',
                'ă' => 'a',
                'ã' => 'a',
                'Ǎ' => 'a',
                'а' => 'a',
                'А' => 'a',
                'å' => 'a',
                'à' => 'a',
                'א' => 'a',
                'Ǻ' => 'a',
                'Ā' => 'a',
                'ǻ' => 'a',
                'ā' => 'a',
                'ä' => 'ae',
                'æ' => 'ae',
                'Ǽ' => 'ae',
                'ǽ' => 'ae',
                'б' => 'b',
                'ב' => 'b',
                'Б' => 'b',
                'þ' => 'b',
                'ĉ' => 'c',
                'Ĉ' => 'c',
                'Ċ' => 'c',
                'ć' => 'c',
                'ç' => 'c',
                'ц' => 'c',
                'צ' => 'c',
                'ċ' => 'c',
                'Ц' => 'c',
                'Č' => 'c',
                'č' => 'c',
                'Ч' => 'ch',
                'ч' => 'ch',
                'ד' => 'd',
                'ď' => 'd',
                'Đ' => 'd',
                'Ď' => 'd',
                'đ' => 'd',
                'д' => 'd',
                'Д' => 'D',
                'ð' => 'd',
                'є' => 'e',
                'ע' => 'e',
                'е' => 'e',
                'Е' => 'e',
                'Ə' => 'e',
                'ę' => 'e',
                'ĕ' => 'e',
                'ē' => 'e',
                'Ē' => 'e',
                'Ė' => 'e',
                'ė' => 'e',
                'ě' => 'e',
                'Ě' => 'e',
                'Є' => 'e',
                'Ĕ' => 'e',
                'ê' => 'e',
                'ə' => 'e',
                'è' => 'e',
                'ë' => 'e',
                'é' => 'e',
                'ф' => 'f',
                'ƒ' => 'f',
                'Ф' => 'f',
                'ġ' => 'g',
                'Ģ' => 'g',
                'Ġ' => 'g',
                'Ĝ' => 'g',
                'Г' => 'g',
                'г' => 'g',
                'ĝ' => 'g',
                'ğ' => 'g',
                'ג' => 'g',
                'Ґ' => 'g',
                'ґ' => 'g',
                'ģ' => 'g',
                'ח' => 'h',
                'ħ' => 'h',
                'Х' => 'h',
                'Ħ' => 'h',
                'Ĥ' => 'h',
                'ĥ' => 'h',
                'х' => 'h',
                'ה' => 'h',
                'î' => 'i',
                'ï' => 'i',
                'í' => 'i',
                'ì' => 'i',
                'į' => 'i',
                'ĭ' => 'i',
                'ı' => 'i',
                'Ĭ' => 'i',
                'И' => 'i',
                'ĩ' => 'i',
                'ǐ' => 'i',
                'Ĩ' => 'i',
                'Ǐ' => 'i',
                'и' => 'i',
                'Į' => 'i',
                'י' => 'i',
                'Ї' => 'i',
                'Ī' => 'i',
                'І' => 'i',
                'ї' => 'i',
                'і' => 'i',
                'ī' => 'i',
                'ĳ' => 'ij',
                'Ĳ' => 'ij',
                'й' => 'j',
                'Й' => 'j',
                'Ĵ' => 'j',
                'ĵ' => 'j',
                'я' => 'ja',
                'Я' => 'ja',
                'Э' => 'je',
                'э' => 'je',
                'ё' => 'jo',
                'Ё' => 'jo',
                'ю' => 'ju',
                'Ю' => 'ju',
                'ĸ' => 'k',
                'כ' => 'k',
                'Ķ' => 'k',
                'К' => 'k',
                'к' => 'k',
                'ķ' => 'k',
                'ך' => 'k',
                'Ŀ' => 'l',
                'ŀ' => 'l',
                'Л' => 'l',
                'ł' => 'l',
                'ļ' => 'l',
                'ĺ' => 'l',
                'Ĺ' => 'l',
                'Ļ' => 'l',
                'л' => 'l',
                'Ľ' => 'l',
                'ľ' => 'l',
                'ל' => 'l',
                'מ' => 'm',
                'М' => 'm',
                'ם' => 'm',
                'м' => 'm',
                'ñ' => 'n',
                'н' => 'n',
                'Ņ' => 'n',
                'ן' => 'n',
                'ŋ' => 'n',
                'נ' => 'n',
                'Н' => 'n',
                'ń' => 'n',
                'Ŋ' => 'n',
                'ņ' => 'n',
                'ŉ' => 'n',
                'Ň' => 'n',
                'ň' => 'n',
                'о' => 'o',
                'О' => 'o',
                'ő' => 'o',
                'õ' => 'o',
                'ô' => 'o',
                'Ő' => 'o',
                'ŏ' => 'o',
                'Ŏ' => 'o',
                'Ō' => 'o',
                'ō' => 'o',
                'ø' => 'o',
                'ǿ' => 'o',
                'ǒ' => 'o',
                'ò' => 'o',
                'Ǿ' => 'o',
                'Ǒ' => 'o',
                'ơ' => 'o',
                'ó' => 'o',
                'Ơ' => 'o',
                'œ' => 'oe',
                'Œ' => 'oe',
                'ö' => 'oe',
                'פ' => 'p',
                'ף' => 'p',
                'п' => 'p',
                'П' => 'p',
                'ק' => 'q',
                'ŕ' => 'r',
                'ř' => 'r',
                'Ř' => 'r',
                'ŗ' => 'r',
                'Ŗ' => 'r',
                'ר' => 'r',
                'Ŕ' => 'r',
                'Р' => 'r',
                'р' => 'r',
                'ș' => 's',
                'с' => 's',
                'Ŝ' => 's',
                'š' => 's',
                'ś' => 's',
                'ס' => 's',
                'ş' => 's',
                'С' => 's',
                'ŝ' => 's',
                'Щ' => 'sch',
                'щ' => 'sch',
                'ш' => 'sh',
                'Ш' => 'sh',
                'ß' => 'ss',
                'т' => 't',
                'ט' => 't',
                'ŧ' => 't',
                'ת' => 't',
                'ť' => 't',
                'ţ' => 't',
                'Ţ' => 't',
                'Т' => 't',
                'ț' => 't',
                'Ŧ' => 't',
                'Ť' => 't',
                '™' => 'tm',
                'ū' => 'u',
                'у' => 'u',
                'Ũ' => 'u',
                'ũ' => 'u',
                'Ư' => 'u',
                'ư' => 'u',
                'Ū' => 'u',
                'Ǔ' => 'u',
                'ų' => 'u',
                'Ų' => 'u',
                'ŭ' => 'u',
                'Ŭ' => 'u',
                'Ů' => 'u',
                'ů' => 'u',
                'ű' => 'u',
                'Ű' => 'u',
                'Ǖ' => 'u',
                'ǔ' => 'u',
                'Ǜ' => 'u',
                'ù' => 'u',
                'ú' => 'u',
                'û' => 'u',
                'У' => 'u',
                'ǚ' => 'u',
                'ǜ' => 'u',
                'Ǚ' => 'u',
                'Ǘ' => 'u',
                'ǖ' => 'u',
                'ǘ' => 'u',
                'ü' => 'ue',
                'в' => 'v',
                'ו' => 'v',
                'В' => 'v',
                'ש' => 'w',
                'ŵ' => 'w',
                'Ŵ' => 'w',
                'ы' => 'y',
                'ŷ' => 'y',
                'ý' => 'y',
                'ÿ' => 'y',
                'Ÿ' => 'y',
                'Ŷ' => 'y',
                'Ы' => 'y',
                'ž' => 'z',
                'З' => 'z',
                'з' => 'z',
                'ź' => 'z',
                'ז' => 'z',
                'ż' => 'z',
                'ſ' => 'z',
                'Ж' => 'zh',
                'ж' => 'zh'
            );
            return strtr($string, $replace);
        };
    }
}
