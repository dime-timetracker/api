<?php

namespace Dime\Server\Filter;

use Dime\Server\Scope\Search as SearchScope;

class Search implements FilterInterface
{
    const NAME = 'search';

    private $map;

    public function __construct(array $map = ['description'])
    {
        $this->map = $map;
    }

    public function name()
    {
        return self::NAME;
    }

    public function __invoke($value)
    {
        return new SearchScope(filter_var($value, FILTER_SANITIZE_STRING), $this->map);
    }
}
