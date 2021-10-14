<?php


namespace CoolRunner\Utils\Mixins;

/**
 * Class Arr
 *
 * @package CoolRunner\Utils\Mixins
 * @internal
 */
class Arr
{

    public function makeArray()
    {
        /**
         * Create an array with a set amount of indices.
         *
         * @param int|array $indices number of indices or an array of keys
         * @param array $keys replacement keys if needed
         *
         * @return array
         * @author Morten K. Harders <mh@coolrunner.dk>
         */
        return function ($indices, $keys = []) {
            if (is_array($indices)) {
                $keys = $indices;
                $indices = count($indices);
            }

            for ($i = 0; $i < $indices; $i++) {
                $return[array_values($keys)[$i] ?? $i] = null;
            }

            return $return ?? [];
        };
    }


    public function formatWrap()
    {
        /**
         * Wrap the contents of an array with a predefined formatting string
         *
         * If the input parameters aren't scalar, then the value will not be wrapped
         *
         * @param \Illuminate\Contracts\Support\Arrayable|array|\ArrayAccess $array Input array
         * @param string $format Formatting string
         * @param array|null $only (Optional) List of keys to wrap - Wraps all if left empty
         *
         * @return mixed Original input
         * @author Morten K. Harders 🐢 <mh@coolrunner.dk>
         */
        return function ($array, string $format = '%s', array $only = null) {
            $tmp = is_object($array) ? clone $array : $array;
            if ($only) {
                $tmp = \Arr::only($tmp, $only);
            }

            foreach ($tmp as $key => $value) {
                if (is_scalar($value)) {
                    $value = sprintf($format, $value);
                }

                $tmp[$key] = $value;
            }

            return $tmp;
        };
    }

    public function fromCsv()
    {
        return function (string $csv_string, $delimiter = ",", $first_line_is_headers = true) {
            $lines = explode(PHP_EOL, $csv_string);
            $array = [];

            $headers = $first_line_is_headers
                ? str_getcsv(array_shift($lines), $delimiter)
                : array_keys(str_getcsv(reset($lines), $delimiter));

            foreach ($lines as $line) {
                $tmpArray = [];
                foreach (str_getcsv($line, $delimiter) as $index => $value) {
                    $tmpArray[$headers[$index] ?? $index] = utf8_encode($value);
                }
                $array[] = $tmpArray;
            }

            return $array;
        };
    }

    public function fromXml()
    {
        return function (string $xml, bool $assoc = true) {
            try {
                return json_decode(json_encode(new \SimpleXMLElement($xml)), $assoc);
            } catch (\Exception $exception) {
                return null;
            }
        };
    }

    public function nestByPrefix()
    {
        return function (string $needle, $array) {
            $nested_array = [];
            foreach ($array as $key => $value) {
                if (strpos($key, $needle) !== false) {
                    $nested_array[str_replace($needle, '', $key)] = $value;
                }
            }
            return $nested_array;
        };
    }

    public function prefix()
    {
        return function (array $array, string $prefix, array $keys = []) {
            return collect($array)->mapWithKeys(function ($e, $i) use ($prefix, $keys) {
                if (($keys && in_array($i, $keys)) || !$keys) {
                    return ["{$prefix}_{$i}" => $e];
                } else {
                    return [$i => $e];
                }
            })->toArray();
        };
    }

    public function flattenWithKeys()
    {

        return function ($array, $keep_assoc_key = true) {
            $collection = [];
            foreach ($array as $assoc_key => $value) {
                if (is_array($value) && \Arr::isAssoc($value)) {
                    foreach ((new Arr())->flattenWithKeys()($value) as $subKey => $subValue) {
                        $keys = [$assoc_key, $subKey];
                        if (!$keep_assoc_key) {
                            array_shift($keys);
                        }

                        $collection[implode("_", $keys)] = $subValue;
                    }
                } else {
                    $collection[$assoc_key] = $value;
                }
            }
            return $collection;
        };
    }

    public function isMultidimensional()
    {
        return function (array $array) {
            return count($array) !== count($array, COUNT_RECURSIVE);
        };
    }

    public function isAssociative()
    {
        return function (array $array) {
            return !($array === []) && array_keys($array) !== range(0, count($array) - 1);
        };
    }

    public function renameKeys()
    {
        return function (array $substitutes, array $source) {
            foreach ($substitutes as $old_key => $new_key) {
                if (!isset($source[$old_key])) {
                    continue;
                }

                $array_keys = array_keys($source);

                $old_key_index = array_search($old_key, $array_keys);
                $array_keys[$old_key_index] = $new_key;

                $source = array_combine($array_keys, $source);
            }

            return $source;
        };
    }

    public function mapKeys()
    {
        return function (array $array, callable $callback) {
            $keys = array_keys($array);
            $values = array_values($array);

            $keys = array_map($callback, $keys);

            return array_combine($keys, $values);
        };
    }

    public function mapKeysRecursive()
    {
        return function (array $array, callable $callback) {
            $array = \Arr::mapKeys($array, $callback);
            foreach ($array as $key => $item) {
                if (is_array($item)) {
                    $array[$key] = \Arr::mapKeysRecursive()($item, $callback);
                }
            }
            return $array;
        };
    }

    /**
     * Makes all combinations of an array - Used for box sizes 📦.
     *
     * @param $elements
     * @return array
     */
    public function permutations()
    {
        return function (array $elements) {
            if (count($elements) <= 1) {
                yield $elements;
            } else {
                foreach (\Arr::permutations(array_slice($elements, 1)) as $permutation) {
                    foreach (range(0, count($elements) - 1) as $i) {
                        yield array_merge(
                            array_slice($permutation, 0, $i),
                            [$elements[0]],
                            array_slice($permutation, $i)
                        );
                    }
                }
            }
        };
    }



    public function mask()
    {
        /**
         * Strips the specified keys from the input array.
         *
         * @param string[] $keys
         * @param array $data
         * @param string $mask
         * @param bool $recursive
         *
         * @return array
         * @author Morten K. Harders 🐢 <mh@coolrunner.dk>
         */
        return function (array $keys, array &$data, string $mask = '[REDACTED]', bool $recursive = true) {
            foreach ($data as $i => &$entry) {
                if (is_string($i) && in_array($i, $keys)) {
                    $data[$i] = $mask;
                } elseif (is_array($entry) && $recursive) {
                    $entry = (new Arr())->mask()($keys, $entry, $mask, $recursive);
                }
            }

            return $data;
        };
    }


    public function toCss()
    {
        /**
         * Recursive function that generates from a a multidimensional array of CSS rules, a valid CSS string.
         *
         * @param array $rules
         *   An array of CSS rules in the form of:
         *   array('selector'=>array('property' => 'value')). Also supports selector
         *   nesting, e.g.,
         *   array('selector' => array('selector'=>array('property' => 'value'))).
         *
         * @return string A CSS string of rules. This is not wrapped in <style> tags.
         * @source http://matthewgrasmick.com/article/convert-nested-php-array-css-string
         */
        return function ($rules, $indent = 0) : string {
            $css = '';
            $prefix = str_repeat('  ', $indent);

            foreach ($rules as $key => $value) {
                if (is_array($value)) {
                    $selector = $key;
                    $properties = $value;

                    $css .= $prefix . "$selector {\n";
                    $css .= $prefix . (new Arr())->toCss()($properties, $indent + 1);
                    $css .= $prefix . "}\n";
                } else {
                    $property = $key;
                    $css .= $prefix . "$property: $value;\n";
                }
            }

            return $css;
        };
    }


}
