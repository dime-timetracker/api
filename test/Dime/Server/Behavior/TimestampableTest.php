<?php

namespace Dime\Server\Behavior;

class TimestampableTest extends \PHPUnit_Framework_TestCase
{

    public function testCreatedAndUpdated()
    {
        $ts = new Timestampable();
        
        $result = $ts([]);
        
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('created_at', $result);
        $this->assertArrayHasKey('updated_at', $result);
    }

    public function testCreatedAndUpdatedRenamed()
    {
        $ts = new Timestampable('c', 'u');
        
        $result = $ts([]);
        
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('c', $result);
        $this->assertArrayHasKey('u', $result);
    }

    public function testCreatedOnly()
    {
        $ts = new Timestampable('created_at', null);
        
        $result = $ts([]);
        
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('created_at', $result);
        $this->assertArrayNotHasKey('updated_at', $result);
    }

    public function testUpdatedOnly()
    {
        $ts = new Timestampable(null, 'updated_at');
        
        $result = $ts([]);
        
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('updated_at', $result);
        $this->assertArrayNotHasKey('created_at', $result);
    }

    public function testNoCreatedChange()
    {
        $ts = new Timestampable();
        
        $result = $ts([ 'created_at' => 'now' ]);
        
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('updated_at', $result);
        $this->assertArrayHasKey('created_at', $result);
        $this->assertEquals('now', $result['created_at']);
    }
}
