<?php

namespace Dime\Server\Transformer;

class Arrayize
{
    private $separator = ';';

    public function __construct($separator = ';')
    {
        $this->separator = $separator;
    }

    public function __invoke($value, $key)
    {
        return (is_string($value)) ? explode($this->separator, $value) : $value;
    }
}
