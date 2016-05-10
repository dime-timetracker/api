<?php

namespace Dime\Api\Filter;

use Dime\Api\Scope\ActivitiyTagsScope;
use Dime\Server\Filter\FilterInterface;

class TagFilter implements FilterInterface
{
    const NAME = 'tag';

    public function name()
    {
        return self::NAME;
    }

    public function __invoke($value)
    {
        return new ActivitiyTagsScope(explode(';', filter_var($value, FILTER_SANITIZE_STRING)));
    }
}
