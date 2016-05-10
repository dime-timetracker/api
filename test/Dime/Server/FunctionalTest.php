<?php

namespace Dime\Server;
    
class FunctionalTest extends \PHPUnit_Framework_TestCase
{
    public function testPositive()
    {
        $this->assertTrue(Functionial\positive(1));
        $this->assertFalse(Functionial\positive(-1));
        $this->assertFalse(Functionial\positive('-1'));
        $this->assertTrue(Functionial\positive('test'));
        $this->assertFalse(Functionial\positive('-test'));
    }

    public function testNegative()
    {
        $this->assertFalse(Functionial\negative(1));
        $this->assertTrue(Functionial\negative(-1));
        $this->assertTrue(Functionial\negative('-1'));
        $this->assertFalse(Functionial\negative('test'));
        $this->assertTrue(Functionial\negative('-test'));
    }

    public function testAbs()
    {
        $this->assertEquals(1, Functionial\abs(1));
        $this->assertEquals(1, Functionial\abs(-1));
        $this->assertEquals('1', Functionial\abs('-1'));
        $this->assertEquals('test', Functionial\abs('test'));
        $this->assertEquals('test', Functionial\abs('-test'));
    }

}
