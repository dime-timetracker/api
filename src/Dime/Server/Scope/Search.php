<?php

namespace Dime\Server\Scope;

use Doctrine\DBAL\Query\QueryBuilder;

class Search
{
    private $text;

    public function __construct($text)
    {
        $this->text = $text;
    }

    public function __invoke(QueryBuilder $qb)
    {
        // tbd
    }
}
