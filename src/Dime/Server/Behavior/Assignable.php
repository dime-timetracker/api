<?php

namespace Dime\Server\Behavior;

class Assignable
{
    private $value;
    private $assignTo;
    
    public function __construct($value, $assignTo = 'user_id')
    {
        $this->value = $value;
        $this->assignTo = $assignTo;
    }

    public function __invoke(array $data = [])
    {        
        $data[$this->assignTo] = $this->value;
        return $data;
    }
}
