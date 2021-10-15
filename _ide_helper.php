<?php

namespace {
  class Num extends \CoolRunner\Utils\Support\Tools\Number {}
  class Bytes extends \CoolRunner\Utils\Support\Tools\Bytes {}
  class Converter extends \CoolRunner\Utils\Support\Tools\Converter {}
  class Coords extends \CoolRunner\Utils\Support\Tools\Coords {}
}

namespace Illuminate\Support {

    class Arr {

        public static function makeArray($indices, $keys = []) {
            /** @var \CoolRunner\Utils\Mixins\Arr $ref */
            return $ref->makeArray($indices, $keys);
        }

        public static function formatWrap($array, string $format = '%s', ?array $only = NULL) {
            /** @var \CoolRunner\Utils\Mixins\Arr $ref */
            return $ref->formatWrap($array, $format, $only);
        }

        public static function fromCsv(string $csv_string, $delimiter = ',', $first_line_is_headers = true) {
            /** @var \CoolRunner\Utils\Mixins\Arr $ref */
            return $ref->fromCsv($csv_string, $delimiter, $first_line_is_headers);
        }

        public static function fromXml(string $xml, bool $assoc = true) {
            /** @var \CoolRunner\Utils\Mixins\Arr $ref */
            return $ref->fromXml($xml, $assoc);
        }

        public static function nestByPrefix(string $needle, $array) {
            /** @var \CoolRunner\Utils\Mixins\Arr $ref */
            return $ref->nestByPrefix($needle, $array);
        }

        public static function prefix(array $array, string $prefix, array $keys = []) {
            /** @var \CoolRunner\Utils\Mixins\Arr $ref */
            return $ref->prefix($array, $prefix, $keys);
        }

        public static function flattenWithKeys($array, $keep_assoc_key = true) {
            /** @var \CoolRunner\Utils\Mixins\Arr $ref */
            return $ref->flattenWithKeys($array, $keep_assoc_key);
        }

        public static function isMultidimensional(array $array) {
            /** @var \CoolRunner\Utils\Mixins\Arr $ref */
            return $ref->isMultidimensional($array);
        }

        public static function isAssociative(array $array) {
            /** @var \CoolRunner\Utils\Mixins\Arr $ref */
            return $ref->isAssociative($array);
        }

        public static function renameKeys(array $substitutes, array $source) {
            /** @var \CoolRunner\Utils\Mixins\Arr $ref */
            return $ref->renameKeys($substitutes, $source);
        }

        public static function mapKeys(array $array, callable $callback) {
            /** @var \CoolRunner\Utils\Mixins\Arr $ref */
            return $ref->mapKeys($array, $callback);
        }

        public static function mapKeysRecursive(array $array, callable $callback) {
            /** @var \CoolRunner\Utils\Mixins\Arr $ref */
            return $ref->mapKeysRecursive($array, $callback);
        }

        public static function permutations(array $elements) {
            /** @var \CoolRunner\Utils\Mixins\Arr $ref */
            return $ref->permutations($elements);
        }

        public static function mask(array $keys, array $data, string $mask = '[REDACTED]', bool $recursive = true) {
            /** @var \CoolRunner\Utils\Mixins\Arr $ref */
            return $ref->mask($keys, $data, $mask, $recursive);
        }

        public static function toCss($rules, $indent = 0) : string {
            /** @var \CoolRunner\Utils\Mixins\Arr $ref */
            return $ref->toCss($rules, $indent);
        }

    }

    class Carbon {

        public static function lastBusinessDay() {
            /** @var \CoolRunner\Utils\Mixins\Dates $ref */
            return $ref->lastBusinessDay();
        }

    }

    class Str {

        public static function utf8Encode($body) {
            /** @var \CoolRunner\Utils\Mixins\Str $ref */
            return $ref->utf8Encode($body);
        }

        public static function replaceLastOccurrence($search, $replace, $subject) {
            /** @var \CoolRunner\Utils\Mixins\Str $ref */
            return $ref->replaceLastOccurrence($search, $replace, $subject);
        }

        public static function detectCsvDelimiter($csv_string, ?array $delimiter_list = NULL) {
            /** @var \CoolRunner\Utils\Mixins\Str $ref */
            return $ref->detectCsvDelimiter($csv_string, $delimiter_list);
        }

        public static function isBase64(string $str) : bool {
            /** @var \CoolRunner\Utils\Mixins\Str $ref */
            return $ref->isBase64($str);
        }

        public static function randomString(int $length, $characters = NULL) : string {
            /** @var \CoolRunner\Utils\Mixins\Str $ref */
            return $ref->randomString($length, $characters);
        }

        public static function normalize(string $string) : string {
            /** @var \CoolRunner\Utils\Mixins\Str $ref */
            return $ref->normalize($string);
        }

    }

}

namespace Illuminate\Support\Facades {

    class Auth {

    }

}

namespace Illuminate\Database\Query {

    class Builder {

        public static function getRawQuery($query) : string {
            /** @var \CoolRunner\Utils\Mixins\Builder $ref */
            return $ref->getRawQuery($query);
        }

        public static function getRawQueryParts($sql, $bindings, $no_quote = false) : string {
            /** @var \CoolRunner\Utils\Mixins\Builder $ref */
            return $ref->getRawQueryParts($sql, $bindings, $no_quote);
        }

    }

}

namespace Illuminate\Database\Eloquent {

    class Builder {

        public static function getRawQuery($query = NULL) {
            /** @var \CoolRunner\Utils\Mixins\Eloquent $ref */
            return $ref->getRawQuery($query);
        }

        public static function getRawQueryParts($sql, $bindings, $no_quote = false) {
            /** @var \CoolRunner\Utils\Mixins\Eloquent $ref */
            return $ref->getRawQueryParts($sql, $bindings, $no_quote);
        }

    }

}
