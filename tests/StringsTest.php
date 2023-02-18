<?php

use FT\Utils\Utils;
use PHPUnit\Framework\TestCase;

final class StringsTest extends TestCase {

    /**
     * @test
     * @dataProvider charAt_args
     */
    public function should_return_charAt_test(string $value, int $index, mixed $expected)
    {
        $this->assertEquals($expected, Utils::$String::charAt($value, $index));
    }

    public static function charAt_args() {
        return [
            ["foobar", 0, 'f'],
            ["foobar", 99, null],
            ["foobar 🤡", 5, 'r'],
            ["🤡", 0, '🤡'],
            ["foobar 🤡", -99, null],
            ["foobar 🤡", -1, '🤡'],
            ["foobar 🤡", -8, 'f'],
            ["foobar 🤡", -5, 'b'],
        ];
    }

}