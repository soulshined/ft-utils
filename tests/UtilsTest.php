<?php

use FT\Utils\Utils;
use PHPUnit\Framework\TestCase;

final class UtilsTest extends TestCase {

    /**
    * @test
    * @dataProvider last_args
    */
    public function should_return_last_test(mixed $value, ?callable $predicate, ?int $qty, mixed $expected) {
        $this->assertEquals($expected, Utils::last($value, predicate: $predicate, qty: $qty));
    }

    /**
    * @test
    * @dataProvider first_args
    */
    public function should_return_first_test(mixed $value, ?callable $predicate, ?int $qty, mixed $expected) {
        $this->assertEquals($expected, Utils::first($value, predicate: $predicate, qty: $qty));
    }

    public static function get_obj() {
        $cls = [
            'aProperty' => 'foobar',
            'bProperty' => [1,2,3],
            'cProperty' => 9.,
        ];
        return (object)$cls;
    }

    public static function last_args() {
        return [
            [[1,2,3], null, null, 3],
            [[1,2,3], null, 2, [2,3]],
            [[1,2,'key' => 'foo'], null, 2, [2,'key' => 'foo']],
            [[1,2,'key' => 'foo'], null, null, ['key' => 'foo']],
            [[1,2,'key' => 'foo'], fn ($key) => is_string($key), null, ['key' => 'foo']],
            [[1,'key' => 'foo', 2], fn ($key, $value) => $value > 1, null, 2],
            [[1,2,'key' => 'foo'], fn ($key, $value) => is_string($key) || $value > 1, 5, ['key' => 'foo', 2]],
            [[1,2,'key' => 'foo'], null, 5, [1, 2, 'key' => 'foo']],

            ["foo bar", null, null, 'r'],
            ["foo bar", null, null, 'r'],
            ["foo bar", null, 3, 'bar'],
            ["foo bar", fn ($i) => $i, 3, 'bar'],

            [static::get_obj(), null, null, ['cProperty' => 9.]],
            [static::get_obj(), null, 2, ['bProperty' => [1,2,3], 'cProperty' => 9.]],
            [static::get_obj(), null, 5, ['bProperty' => [1,2,3], 'cProperty' => 9., 'aProperty' => 'foobar']],
        ];
    }

    public static function first_args() {
        return [
            [[1,2,3], null, null, 1],
            [[1,2,3], null, 2, [1,2]],
            [['key' => 'foo', 1,2], null, 2, ['key' => 'foo', 1]],
            [['key' => 'foo', 1,2], null, null, ['key' => 'foo']],
            [[1, 'key' => 'foo', 2], fn ($key) => is_string($key), null, ['key' => 'foo']],
            [['key' => 'foo', 1, 2], fn ($key, $value) => (int)$value > 1, null, 2],
            [['key' => 'foo', 1,2], fn ($key, $value) => is_string($key) || $value > 1, 5, ['key' => 'foo', 2]],
            [['key' => 'foo', 1,2], null, 5, [1, 2, 'key' => 'foo']],

            ["foo bar", null, null, 'f'],
            ["foo bar", null, null, 'f'],
            ["foo bar", null, 3, 'foo'],
            ["foo bar", fn ($i) => $i, 3, 'foo'],

            [static::get_obj(), null, null, ['aProperty' => 'foobar']],
            [static::get_obj(), null, 2, ['aProperty' => 'foobar', 'bProperty' => [1,2,3]]],
            [static::get_obj(), null, 5, ['aProperty' => 'foobar', 'bProperty' => [1,2,3], 'cProperty' => 9.]],
        ];
    }

}