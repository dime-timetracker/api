<?php

namespace Dime\Server\Validator;

class Validator
{
    private $validators = [];
    private $errors = [];

    public function add($validator, $name = null)
    {
        if (!is_callable($validator, true, $callableName)) {
            throw new \Exception("Validator is not a callable");
        }
        
        if (empty($name)) {
            $name = $callableName;
        }
        
        $this->validators[$name] = $validator;
    }
    
    public function getErrors()
    {
        return $this->errors;
    }

    public function validate(array $data)
    {
        $result = true;
        foreach ($this->validators as $functor) {
            if (!call_user_func($functor, $data, $this->errors)) {
                $result = false;
                break;
            }
        }
        return $result;
    }
}
