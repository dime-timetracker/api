<?php

namespace Dime\Server\Transformer;

class ExpireTransformer
{
    public function __invoke(array $value, $key)
    {
        $value['expires'] = $value['updated_at'];
        unset($value['created_at']);
        unset($value['updated_at']);
        
        return $value;
    }
}
