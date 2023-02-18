<?php

final class Arrays {

    /**
     * This method does not preserve keys
     *
     * @param int $depth use `-1` to flatten indefinitely. Defaults to 1
     */
    public static function flatten(array $haystack, int $depth = 1) : array {
        $flattened = [];
        $should_flatten = $depth === -1 || $depth-- > 0;

        foreach ($haystack as $value) {
            if ($should_flatten && is_array($value)) {
                $values = static::flatten(array_values($value), $depth);
                array_push($flattened, ...$values);
            } else $flattened[] = $value;
        }

        return $flattened;
    }

    public static function anyMatch(array $haystack, callable $predicate) : bool {
        foreach($haystack as $i)
            if (call_user_func($predicate, $i) === true) return true;

        return false;
    }

    public static function allMatch(array $haystack, callable $predicate) : bool {
        foreach ($haystack as $i)
            if (call_user_func($predicate, $i) !== true) return false;

        return true;
    }

    public static function noneMatch(array $haystack, callable $predicate) : bool {
        foreach ($haystack as $i)
            if (call_user_func($predicate, $i) !== false) return false;

        return true;
    }

    public static function assc_array_map(array $haystack, callable $callback): array {
        return @array_map($callback, array_keys($haystack), $haystack);
    }

    /**
     * @return mixed the first element found that matches the property value pairs (pvps) given
     */
    public static function find_by_property_values(array $haystack, array $pvps) : mixed {
        if (empty($pvps)) return null;

        foreach ($haystack as $value) {
            $matches = 0;
            foreach ($pvps as $key => $needle) {
                if (!property_exists($value, $key)) continue;
                if ($value->$key == $needle) $matches++;
            }
            if ($matches === count($pvps)) return $value;
        }

        return null;
    }

    public static function sort_by_property(array &$haystack, string $property, string $direction = 'asc') {
        if (in_array(strtolower($direction), ['dsc', 'desc', 'descending']))
            uasort($haystack, fn ($a, $b) => (@$b->{$property} ?? 0) <=> ($a->{$property} ?? 0));
        else
            uasort($haystack, fn ($a, $b) => (@$a->{$property} ?? 0) <=> (@$b->{$property} ?? 0));

        $haystack = array_values($haystack);
    }

    public static function sort_by_value_key(array &$haystack, string $key, string $direction = 'asc') {
        if (in_array(strtolower($direction), ['dsc', 'desc', 'descending']))
            uasort($haystack, fn ($a, $b) => ($b[$key] ?? 0) <=> ($a[$key] ?? 0));
        else
            uasort($haystack, fn ($a, $b) => (@$a[$key] ?? 0) <=> (@$b[$key] ?? 0));

        $haystack = array_values($haystack);
    }

}