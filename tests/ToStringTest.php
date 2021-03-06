<?php

namespace theodorejb\polycast;

class ToStringTest extends \PHPUnit_Framework_TestCase
{
    public function shouldPass()
    {
        return [
            ["foobar", "foobar"],
            ["123", 123],
            ["123.45", 123.45],
            ["INF", INF],
            ["-INF", -INF],
            ["NAN", NAN],
            ["", ""],
            ["foobar", new Stringable()],
        ];
    }

    /**
     * @dataProvider shouldPass
     */
    public function testShouldPass($expected, $val)
    {
        $this->assertTrue(safe_string($val));
        $this->assertSame($expected, to_string($val));
    }

    public function disallowedTypes()
    {
        return [
            [null],
            [true],
            [false],
            [fopen("data:text/html,foobar", "r")],
            [[]],
        ];
    }

    /**
     * @dataProvider disallowedTypes
     * @expectedException theodorejb\polycast\CastException
     */
    public function testDisallowedTypes($val)
    {
        $this->assertFalse(safe_string($val));
        to_string($val);
    }

    public function invalidObjects()
    {
        return [
            [new \stdClass()],
            [new NotStringable()],
        ];
    }

    /**
     * @dataProvider invalidObjects
     * @expectedException theodorejb\polycast\CastException
     */
    public function testInvalidObjects($val)
    {
        $this->assertFalse(safe_string($val));
        to_string($val);
    }
}

class NotStringable {}

class Stringable
{
    public function __toString()
    {
        return "foobar";
    }
}
