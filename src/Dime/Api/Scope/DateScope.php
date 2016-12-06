<?php

namespace Dime\Api\Scope;

use PDO;
use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class DateScope
{
    private $start;
    private $end;

    private $format = 'Y-m-d H:i:s';

    public function __construct(DateTime $start, DateTime $end = null)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function __invoke(QueryBuilder $qb)
    {
        if (!empty($this->start)) {
            $qb->andWhere($qb->expr()->gte('started_at', ':start'))->setParameter('start', $this->start->format($this->format));
        }

        if (!empty($this->end)) {
            $qb->andWhere($qb->expr()->lte('started_at', ':end'))->setParameter('end', $this->end->format($this->format));
        }

        return $qb;
    }
}
