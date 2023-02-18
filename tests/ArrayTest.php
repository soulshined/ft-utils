<?php

use FT\Utils\Utils;
use PHPUnit\Framework\TestCase;

final class ArrayTest extends TestCase {

    /**
    * @test
    * @dataProvider flatten_args
    */
    public function should_flatten_test(array $array, array $expected, int $depth = 1) {
        $this->assertEquals($expected, Utils::$Array::flatten($array, $depth));
    }

    public static function flatten_args() {
        return [
            [[], []],
            [[1,2,3], [1,2,3]],
            [[1,2,[3]], [1,2,3]],
            [[1,2,[3, [4]]], [1,2,3,[4]]],
            [[1,2,[3, [4, [5,[6,[7]]]]]], [1,2,3,4,5,[6,[7]]], 3],
            [[1,2,[3, [4, [5,[6,[7]]]]]], [1,2,3,4,5,6,7], -1],
        ];
    }


    /**
    * @test
    */
    public function any_match_test() {
        $this->assertTrue(Utils::$Array::anyMatch([1,2,3], fn($i) => $i > 0));
        $this->assertFalse(Utils::$Array::anyMatch([1,2,3], fn($i) => $i < 0));
        $this->assertFalse(Utils::$Array::anyMatch([1,2,3], fn($i) => 1));
        $this->assertFalse(Utils::$Array::anyMatch([1,2,3], fn($i) => 0));
    }

    /**
    * @test
    */
    public function all_match_test() {
        $this->assertTrue(Utils::$Array::allMatch([1, 2, 3], fn ($i) => $i > 0));
        $this->assertTrue(Utils::$Array::allMatch([1, 2, 3], fn ($i) => $i < 4));
        $this->assertFalse(Utils::$Array::allMatch([1, 2, 3], fn ($i) => $i < 0));
        $this->assertFalse(Utils::$Array::allMatch([1, 2, 3], fn ($i) => 1));
        $this->assertFalse(Utils::$Array::allMatch([1, 2, 3], fn ($i) => 0));
    }

    /**
    * @test
    */
    public function none_match_test() {
        $this->assertFalse(Utils::$Array::noneMatch([1, 2, 3], fn ($i) => $i < 4));
        $this->assertFalse(Utils::$Array::noneMatch([1, 2, 3], fn ($i) => $i > 0));
        $this->assertTrue(Utils::$Array::noneMatch([1, 2, 3], fn ($i) => $i < 0));
        $this->assertFalse(Utils::$Array::noneMatch([1, 2, 3], fn ($i) => 1));
        $this->assertFalse(Utils::$Array::noneMatch([1, 2, 3], fn ($i) => 0));
    }

    /**
    * @test
    */
    public function assc_array_map_test() {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];

        $this->assertEquals([
            'Content-Type: application/json',
            'Accept: application/json'
        ], Utils::$Array::assc_array_map($headers, fn ($k, $v) => "$k: $v"));
    }

    /**
    * @test
    */
    public function find_by_property_values_test() {
        $users=  $this->getUserObjects();

        $this->assertEquals($users[0], Utils::$Array::find_by_property_values($users, [
            'lname' => 'doe'
        ]));

        $this->assertEquals($users[0], Utils::$Array::find_by_property_values($users, [
            'lname' => 'doe',
            'state' => 'AZ'
        ]));

        $this->assertEquals($users[1], Utils::$Array::find_by_property_values($users, [
            'lname' => 'doe',
            'state' => 'AZ',
            'age' => 77
        ]));

        $this->assertEquals(null, Utils::$Array::find_by_property_values($users, [
            'lname' => 'doe',
            'state' => 'AZ',
            'age' => 79
        ]));

        $this->assertEquals(null, Utils::$Array::find_by_property_values($users, []));
    }

    /**
    * @test
    */
    public function sort_by_property_test() {
        $users= $this->getUserObjects();

        Utils::$Array::sort_by_property($users, 'age');

        $this->assertEquals('jerry', $users[0]->fname);
        $this->assertEquals('john', $users[3]->fname);

        Utils::$Array::sort_by_property($users, 'age', 'desc');

        $this->assertEquals('john', $users[0]->fname);
        $this->assertEquals('jerry', $users[3]->fname);
    }

    /**
    * @test
    */
    public function sort_by_value_key_test() {
        $users= array_map(fn ($i) => (array)$i, $this->getUserObjects());

        Utils::$Array::sort_by_value_key($users, 'age');

        $this->assertEquals('jerry', $users[0]['fname']);
        $this->assertEquals('john', $users[3]['fname']);

        Utils::$Array::sort_by_value_key($users, 'age', 'desc');

        $this->assertEquals('john', $users[0]['fname']);
        $this->assertEquals('jerry', $users[3]['fname']);
    }

    private function getUserObjects() {
        return [
            (object)[
                'fname' => 'john',
                'lname' => 'doe',
                'age' => 99,
                'state' => 'AZ'
            ],
            (object)[
                'fname' => 'jane',
                'lname' => 'doe',
                'age' => 77,
                'state' => 'AZ'
            ],
            (object)[
                'fname' => 'jerry',
                'lname' => 'doe',
                'age' => 66,
                'state' => 'CA'
            ],
            (object)[
                'fname' => 'jorge',
                'lname' => 'doe',
                'age' => 88,
                'state' => 'NV'
            ],
        ];
    }

}