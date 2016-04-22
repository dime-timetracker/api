<?php

namespace Dime\Server\Validator;

class Validator
{
    private $errors = [];
    protected $runnables = [];

    public function prepare($function, $name = null)
    {
        if (!is_callable($function, true, $callableName)) {
            throw new Exception("Function can not be called. Use a callable.");
        }

        if (empty($name)) {
            $name = $callableName;
        }

        $this->runnables[$name] = $function;
    }
    
    public function getErrors()
    {
        return $this->errors;
    }

    public function validate($data)
    {
        $result = true;
        $errors = [];
        foreach ($this->runnables as $function) {
            if (!call_user_func($function, $data, $errors)) {
                $result = false;
                break;
            }
        }
        $this->errors = $errors;
        return $result;
    }
}
