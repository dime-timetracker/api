<?php

namespace Dime\Server\Repository;

use Dime\Server\Chain;
use Dime\Server\Stream;

class ActivitiesResourceRepository extends ResourceRepository
{

    const NAME = 'activities';
    const RELATION_TIMESLICE = 'timeslices';
    const RELATION_TAGS = 'activity_tags';

    public function __construct(\Doctrine\DBAL\Connection $connection)
    {
        parent::__construct($connection, self::NAME);
    }

    public function find(array $identifier)
    {
        $activitiy = parent::find($identifier);
        return Chain::it($activitiy)
                        ->with([$this, 'retrieveTimeslices'])
                        ->with([$this, 'retrieveTags'])
                        ->andRun();
    }

    public function findAll(array $filters = array(), $page = 1, $with = 0)
    {
        $activities = parent::findAll($filters, $page, $with);

        return Stream::of($activities)
                        ->map([$this, 'retrieveTimeslices'])
                        ->map([$this, 'retrieveTags'])
                        ->collect();
    }

    public function retrieveTimeslices(array $activity)
    {
        if (!empty($activity) && isset($activity['id'])) {
            $qb = $this->getConnection()->createQueryBuilder()->select('*')->from(self::RELATION_TIMESLICE);
            $qb->where($qb->expr()->eq('activity_id', ':activity_id'))->setParameter('activity_id', $activity['id']);

            $activity['timeslices'] = Stream::of($qb->execute()->fetchAll())->collect();
        }

        return $activity;
    }

    public function retrieveTags(array $activity)
    {
        if (!empty($activity) && isset($activity['id'])) {
            $qb = $this->getConnection()->createQueryBuilder()->select('*')->from(self::RELATION_TAGS);
            $qb->where($qb->expr()->eq('activity_id', ':activity_id'))->setParameter('activity_id', $activity['id']);
            $activity['tags'] = Stream::of($qb->execute()->fetchAll())->map([$this, 'flattenTag'])->collect();
        }

        return $activity;
    }

    public function flattenTag(array $tag)
    {
        return $tag['tag_id'];
    }

}
