<?php

namespace Dime\Server\Behavior;

use Dime\Server\Stream\Stream;

class BehaviorFactory
{
    private $config = [];
    
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }
    
    public function build($resource)
    {
        $result = new Behavior();
        if (isset($this->config[$resource]) && isset($this->config[$resource]['behave'])) {
            Stream::of($this->config[$resource]['behave'])->each($result->add);
        }        
        return $result;
    }
}
