<?php

namespace Dime\Server\Validator;

class RequiredTest extends \PHPUnit_Framework_TestCase
{
    public function testRequiredFalse()
    {
        $required = new Required(['alias']);

        $errors = $required(['name' => 'test']);
        $this->assertCount(1, $errors);
        $this->assertEquals('The field [alias] is required.', $errors['alias']);
    }

    public function testRequiredTrue()
    {
        $required = new Required(['alias']);

        $this->assertCount(0, $required(['alias' => 'test']));
    }
}
