<?php

namespace Dime\Server;

class Validator
{
    protected $runnables = [];

    public function __construct(array $validators = [])
    {
        foreach ($validators as $name => $function) {
            $callername = null;
            if (is_string($name)) {
                $callername = $name;
            }

            $this->prepare($function, $callername);
        }
    }

    public function prepare($function, $name = null)
    {
        if (!is_callable($function, true, $callableName)) {
            throw new Exception('Function can not be called. Use a callable.');
        }

        if (empty($name)) {
            $name = $callableName;
        }

        $this->runnables[$name] = $function;
    }

    public function validate($data)
    {
        $errors = [];
        foreach ($this->runnables as $name => $function) {
            $result = call_user_func($function, $data);
            if (!empty($result)) {
                $errors[$name] = $result;
            }
        }
        return $errors;
    }

    public function __invoke($data)
    {
        return $this->validate($data);
    }
}
