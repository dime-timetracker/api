<?php

namespace Dime\Server\Filter;

class Filter
{
    private $filters = [];

    public function add($filter, $name = null)
    {
        if (!is_callable($filter, true, $callableName)) {
            throw new Exception("Validator is not a callable");
        }
        
        if (empty($name)) {
            $name = $callableName;
        }
        
        $this->filters[$name] = $filter;
    }
    
    public function execute(array $data)
    {
        Stream::of($this->filters)->each(function ($value, $key) use ($data) {
            call_user_func($value, $data);
        });
        
        return $data;
    }
}
