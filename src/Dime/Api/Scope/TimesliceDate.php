<?php

namespace Dime\Api\Scope;

use PDO;
use DateTime;
use Dime\Server\Scope\In;
use Doctrine\DBAL\Query\QueryBuilder;

class TimesliceDate
{
    const TABLE = 'timeslices';

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
        $qbTimeslice = $qb
                ->getConnection()->createQueryBuilder()
                ->from(self::TABLE)
                ->select('DISTINCT activity_id');

        if (!empty($this->start)) {
            $qbTimeslice->andWhere($qb->expr()->gte('started_at', ':start'))->setParameter('start', $this->start->format($this->format));
        }

        if (!empty($this->end)) {
            $qbTimeslice->andWhere($qb->expr()->lte('started_at', ':end'))->setParameter('end', $this->end->format($this->format));
        }

        $activityIds = $qbTimeslice->execute()->fetchAll(PDO::FETCH_COLUMN);
        if (!empty($activityIds)) {
            $inScope = new In('id', $activityIds);
            $qb = $inScope($qb);
        } else {
            throw new \Exception('No data');
        }

        return $qb;
    }
}
