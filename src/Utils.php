<?php

namespace FT\Utils;

use Arrays;
use Path;
use Preg;
use Strings;

foreach([
    'Arrays',
    'Path',
    'Preg',
    'Strings',
] as $util) {
    require_once __DIR__ . "/./$util.php";
}

final class Utils {

    public static Strings $String;
    public static Preg $Regex;
    public static Path $Path;
    public static Arrays $Array;

    public static function guidv4($data = NULL)
    {
        $data = $data ?? openssl_random_pseudo_bytes(16);
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Return the last segment of a given value
     *
     * If `$value` is an array this will return the last element in the array
     * If `$value` is an object this will return whatever the last property is
     * If `$value` is a string this will return the last character
     * Otherwise this will return null
     *
     * @param mixed $value triggers a warning for any data type other than object, string, array
     * @param callable $predicate predicates have no affect on a string `$value`
     * @param int $qty specifies how many results to return, defaults to 1
     *
     * @return mixed Any element that is last as defined. If an associative array, the key and value is returned, otherwise just the value. If an object, the property name and value are always returned.
     */
    public static function last(mixed $value, ?callable $predicate = null, ?int $qty = null) : mixed {
        if (empty($value)) return null;
        $fqty = $qty ?? 1;
        if ($fqty < 1) $fqty = 1;

        if (is_object($value))
            $value = get_object_vars($value);

        if (is_array($value)) {
            if (!is_null($predicate)) {
                $out = [];
                $keys = array_keys($value);
                for ($i=count($keys) - 1; $i >= 0; --$i) {
                    $k = $keys[$i];
                    $v = $value[$k];
                    if (call_user_func($predicate, $k, $v) === true) {
                        if (is_string($k))
                            $out[$k] = $v;
                        else array_unshift($out, $v);
                        if (count($out) === $fqty) {
                            $key = array_keys($out)[0];
                            $value = array_values($out)[0];
                            if (is_null($qty)) {
                                if (is_string($key)) return [$key => $value];
                                return $value;
                            }
                            return $out;
                        }
                    }
                }

                return is_null($qty) ? null : $out;
            } else {
                $slice = array_slice($value, -$fqty);
                if (is_null($qty)) {
                    $key = array_keys($slice)[0];
                    $v = array_values($slice)[0];
                    if (is_string($key)) return [$key => $v];
                    return $v;
                }
                return $slice;
            }
        } else if (is_string($value)) {
            return substr($value, -$fqty);
        } else {
            trigger_error("Unsupported value type " . gettype($value), E_USER_WARNING);
        }

        return null;
    }

    /**
     * Return the first segment of a given value
     *
     * If `$value` is an array this will return the first element in the array
     * If `$value` is an object this will return whatever the first property is
     * If `$value` is a string this will return the first character
     * Otherwise this will return null
     *
     * @param mixed $value triggers a warning for any data type other than object, string, array
     * @param callable $predicate predicates have no affect on a string `$value`
     * @param int $qty specifies how many results to return, defaults to 1
     *
     * @return mixed Any element that is first as defined. If an associative array, the key and value is returned, otherwise just the value. If an object, the property name and value are always returned.
     */
    public static function first(mixed $value, ?callable $predicate = null, ?int $qty = null): mixed {
        if (empty($value)) return null;
        $fqty = $qty ?? 1;
        if ($fqty < 1) $fqty = 1;

        if (is_object($value))
            $value = get_object_vars($value);

        if (is_array($value)) {
            if (!is_null($predicate)) {
                $out = [];
                foreach ($value as $k => $v) {
                    if (call_user_func($predicate, $k, $v) === true) {
                        if (!is_string($k))
                            $out[] = $v;
                        else $out[$k] = $v;
                        if (count($out) === $fqty) {
                            $key = array_keys($out)[0];
                            $value = array_values($out)[0];
                            if (is_null($qty)) {
                                if (is_string($key)) return [$key => $value];
                                return $value;
                            }
                            return $out;
                        }
                    }
                }

                return is_null($qty) ? null : $out;
            } else {
                $slice = array_slice($value, 0, $fqty);
                if (is_null($qty)) {
                    $key = array_keys($slice)[0];
                    $v = array_values($slice)[0];
                    if (is_string($key)) return [$key => $v];

                    return $v;
                }
                return $slice;
            }
        }
        else if (is_string($value)) {
            return substr($value, 0, $fqty);
        } else {
            trigger_error("Unsupported value type " . gettype($value), E_USER_WARNING);
        }

        return null;
    }

}

Utils::$Array = new Arrays;
Utils::$Path = new Path;
Utils::$Regex = new Preg;
Utils::$String = new Strings;