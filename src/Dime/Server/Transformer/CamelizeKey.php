<?php

namespace Dime\Server\Transformer;

class CamelizeKey
{
    private $separator = '_';

    public function __construct($separator = '_')
    {
        $this->separator = $separator;
    }

    public function __invoke($value, $key)
    {
        return str_replace($this->separator, '', lcfirst(ucwords($key, $this->separator)));
    }
}
