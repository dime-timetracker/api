<?php

namespace Dime\Server\Transformer;

class DecamelizeKey
{

    public function __invoke($value, $key)
    {
        return strtolower(
            preg_replace(
                ['/([A-Z]+)/', '/_([A-Z]+)([A-Z][a-z])/'],
                ['_$1', '_$1_$2'],
                lcfirst($key)
            )
        );
    }

}
