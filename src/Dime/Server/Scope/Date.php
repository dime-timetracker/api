<?php

namespace Dime\Server\Scope;

use DateTime;
use Doctrine\DBAL\Query\QueryBuilder;

class Date
{
    private $start;
    private $end;

    public function __construct(DateTime $start, DateTime $end = null)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function __invoke(QueryBuilder $qb)
    {
        // tbd
    }
}
