<?php

use FT\Utils\Utils;
use PHPUnit\Framework\TestCase;

final class PregTest extends TestCase {

    /**
    * @test
    */
    public function match_all_test() {
        $multiline = <<<'A'
https://johndoe:pa$$w0rd@example.com

https://janedoe:pa$$w0rd@example.com
A;

        Utils::$Regex::match_all('/https:\/\/(?:(?<username>\w+):(?<password>\S+))@example\.com/', $multiline, $matches);

        $this->assertEquals(2, count($matches));

        $match1 = $matches[0];
        $this->assertEquals('https://johndoe:pa$$w0rd@example.com', $match1->value);
        $this->assertEquals(0, $match1->start);
        $this->assertEquals(36, $match1->end);

        $this->assertEquals("johndoe", $match1->groups->{'1'});
        $this->assertEquals("johndoe", $match1->groups->username);
        $this->assertEquals("johndoe", $match1->g1);

        $this->assertEquals('pa$$w0rd', $match1->groups->{'2'});
        $this->assertEquals('pa$$w0rd', $match1->groups->password);
        $this->assertEquals('pa$$w0rd', $match1->g2);

        $this->assertNull(@$match1->groups->{'0'});
        $this->assertNull(@$match1->groups->{'3'});

        $match2 = $matches[1];
        $this->assertEquals('https://janedoe:pa$$w0rd@example.com', $match2->value);
        $this->assertEquals(40, $match2->start);
        $this->assertEquals(76, $match2->end);

        $this->assertEquals("janedoe", $match2->groups->{'1'});
        $this->assertEquals("janedoe", $match2->groups->username);
        $this->assertEquals("janedoe", $match2->g1);

        $this->assertEquals('pa$$w0rd', $match2->groups->{'2'});
        $this->assertEquals('pa$$w0rd', $match2->groups->password);
        $this->assertEquals('pa$$w0rd', $match2->g2);

        $this->assertNull(@$match2->groups->{'0'});
        $this->assertNull(@$match2->groups->{'3'});
    }

    /**
    * @test
    * @dataProvider count_args
    */
    public function count_test($pattern, $subject, $expected) {
        $count = Utils::$Regex::count($pattern, $subject);
        $this->assertEquals($expected, $count);
    }

    public static function count_args() {
        return [
            ["", "12345", 0],
            ["d", "12345", 0],
            ["/\d/", "12345", 5],
            ["/\d+/", "12345", 1],
            ["/\n/", "Line 1\r\nLine2\r\nLine3\r\n", 3],
            ["/(/", "abc", 0],
        ];
    }


}