<?php

namespace Dime\Server\Scope;

use DateTime;
use Doctrine\DBAL\Query\QueryBuilder;

class Date
{
    private $start;
    private $end;
    private $map;

    public function __construct(DateTime $start, DateTime $end = null, array $map = ['start' => 'updated_at', 'end' => 'updated_at'])
    {
        $this->start = $start;
        $this->end = $end;
        $this->map = $map;
    }

    public function __invoke(QueryBuilder $qb)
    {
        if (!empty($this->start)) {
            $qb->andWhere($qb->expr()->gte($this->map['start'], ':start'))->setParameter('start', $this->start);
        }

        if (!empty($this->end)) {
            $qb->andWhere($qb->expr()->lte($this->map['end'], ':end'))->setParameter('end', $this->end);
        }

        return $qb;
    }
}
