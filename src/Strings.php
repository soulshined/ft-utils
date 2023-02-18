<?php

final class Strings {

    /**
     * @param int $index supports positive and negative values
     */
    public static function charAt(string $value, int $index) : ?string {
        if (empty($value)) return null;

        $utf8 = static::utf8($value);
        if ($index < 0 && abs($index) > mb_strlen($utf8)) return null;
        else if ($index > mb_strlen($utf8) -1) return null;

        return mb_substr($utf8, $index, 1);
    }

    public static function utf8(string $value): ?string {
        return mb_convert_encoding($value, 'UTF-8');
    }

    /**
     * @param ?callable $foreach is applied to the exploded result
     * @param ?string $implode_delimiter if null uses $explode_delimiter
     */
    public static function explode_implode(
            string $value,
            string $explode_delimiter,
            ?callable $foreach = null,
            ?string $implode_delimiter = null,
            int $limit = PHP_INT_MAX
        ) : string {
        $implode_delimiter = $implode_delimiter ?? $explode_delimiter;

        if (is_null($foreach))
           return implode($implode_delimiter, explode($explode_delimiter, $value, $limit));

        return implode($implode_delimiter,
            array_map($foreach, explode($explode_delimiter, $value, $limit))
        );
    }

}