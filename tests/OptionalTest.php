<?php

use FT\Utils\Absent;
use FT\Utils\Optional;
use PHPUnit\Framework\TestCase;

final class OptionalTest extends TestCase {

    /**
    * @test
    */
    public function should_throw_for_null_test() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("No value present");
        Optional::of(null);
    }

    /**
    * @test
    */
    public function should_throw_for_using_absent_test() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(Absent::class);
        Optional::of(new Absent);
    }

    /**
    * @test
    */
    public function should_be_present_test() {
        $op = Optional::of(9.);
        $this->assertTrue($op->isPresent());
        $this->assertEquals(9., $op->get());
    }

    /**
    * @test
    */
    public function or_else_test()
    {
        $op = Optional::of(9.);
        $this->assertTrue($op->isPresent());
        $this->assertEquals(9., $op->get());

        $op = Optional::ofNullable(null);
        $this->assertFalse($op->isPresent());
        $this->assertEquals(1., $op->orElse(1.));
    }

    /**
    * @test
    */
    public function or_else_throw_good_test() {
        $op = Optional::of(9.);
        $this->assertEquals(9., $op->orElseThrow());
    }

    /**
    * @test
    */
    public function or_else_throw_should_throw_test() {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("No value present");
        $op = Optional::ofNullable(null);
        $op->orElseThrow();
    }

    /**
    * @test
    */
    public function if_present_null_test() {
        $total = 9;
        $op = Optional::ofNullable(null);
        $op->ifPresent(function ($i) use (&$total) { $total += 99; });

        $this->assertEquals(9, $total);
    }

    /**
    * @test
    */
    public function if_present_orelse_test() {
        $total = 9;
        $op = Optional::ofNullable(null);
        $op->ifPresentOrElse(
            function ($i) use (&$total) { $total += 99; },
            function () use (&$total) { $total = 0; }
        );

        $this->assertEquals(0, $total);
    }

    /**
    * @test
    */
    public function if_present_test() {
        $total = 9;
        $op = Optional::ofNullable(1);
        $op->ifPresent(function ($i) use (&$total) { $total+= $i; });

        $this->assertEquals(10, $total);
    }

    /**
    * @test
    */
    public function should_map_test() {
        $op = Optional::of("foobar");

        $this->assertEquals("foobar-updated", $op->map(fn ($i) => "$i-updated")->get());
    }

    /**
    * @test
    */
    public function equals_test() {
        $aOp = Optional::ofNullable(null);
        $bOp = Optional::ofNullable(10);

        $this->assertFalse($aOp->equals($bOp));

        $aOp = Optional::of(9);
        $this->assertFalse($aOp->equals($bOp));

        $aOp = Optional::of(10);
        $this->assertTrue($aOp->equals($bOp));

        $aOp = Optional::of([1,2,[3,4]]);
        $bOp = Optional::of([1,2,[3,4]]);
        $this->assertTrue($aOp->equals($bOp));
    }

}