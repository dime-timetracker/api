<?php

namespace Dime\Server;
    
class StreamTest extends \PHPUnit_Framework_TestCase
{
    public function testMatchAll()
    {
        $stream = Stream::of([ 'key1' => 'value', 'key2' => 'value']);
        
        $this->assertTrue($stream->matchAll(function ($value, $key) {
            return $value == 'value';
        }));
        
        $this->assertFalse($stream->matchAll(function ($value, $key) {
            return $key == 'key';
        }));
    }
    
    public function testMatchAny()
    {
        $stream = Stream::of([ 'key1' => 'value', 'key2' => 'value']);
        
        $this->assertTrue($stream->matchAny(function ($value, $key) {
            return $value == 'value';
        }));
        
        $this->assertFalse($stream->matchAll(function ($value, $key) {
            return $key == 'key';
        }));
    }
    
    public function testMap()
    {
        $result = Stream::of([ 'key' => 'value'])->map(function ($value, $key) {
            return 'value1';
        })->collect();
        
        $this->assertArrayHasKey('key', $result);
        $this->assertEquals('value1', $result['key']);
    }
    
    public function testRemap()
    {
        $result = Stream::of([ 'key' => 'value'])->remap(function ($value, $key) {
            return 'key1';
        })->collect();
        
        $this->assertArrayHasKey('key1', $result);
        $this->assertEquals('value', $result['key1']);
    }

}
