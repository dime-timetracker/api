<?php

namespace Dime\Server;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testValidatorWithConstructor()
    {
        $validator = new Validator([
           'required' => new Validator\Required(['alias'])
        ]);

        $errors = $validator(['name' => 'test']);
        $this->assertCount(1, $errors);
        $this->assertEquals('The field [alias] is required.', $errors['required']['alias']);
    }
}
