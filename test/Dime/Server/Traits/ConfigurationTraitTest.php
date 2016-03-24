<?php

namespace Dime\Server\Traits;

class ConfigurationTraitTest extends \PHPUnit_Framework_TestCase
{
    private $traitObject;

    public function setUp()
    {
        $this->traitObject = $this->getObjectForTrait(__NAMESPACE__ . '\ConfigurationTrait');
        $this->traitObject->setConfig([
            'test' => [
                '1' => [
                    'name' => 'value'
                ]
            ]
        ]);
    }

    public function testGetConfigValue()
    {
        $expected = 'value';
        $actual = $this->traitObject->getConfigValue(['test', '1', 'name']);

        $this->assertEquals($expected, $actual);
    }

    public function testGetConfigValueDefault()
    {
        $expected = 'default';
        $actual = $this->traitObject->getConfigValue(['test', '2', 'name'], 'default');

        $this->assertEquals($expected, $actual);
    }

    public function testGetConfigValueNotFound()
    {
        $expected = null;
        $actual = $this->traitObject->getConfigValue(['test', '2', 'name']);

        $this->assertEquals($expected, $actual);
    }

}