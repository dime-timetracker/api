<?php

namespace Dime\Server;

class Behavior
{
    private $behaviors = [];
    
    public function add($behavior, $name = null)
    {
        if (!is_callable($behavior, true, $callableName)) {
            throw new Exception("Validator is not a callable");
        }
        
        if (empty($name)) {
            $name = $callableName;
        }
        
        $this->behaviors[$name] = $behavior;
    }
    
    public function execute(array $data)
    {
        foreach ($this->behaviors as $function) {
            $data = call_user_func($function, $data);
        }
                
        return $data;
    }
}
