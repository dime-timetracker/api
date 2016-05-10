<?php

namespace Dime\Api\Scope;

use PDO;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class ActivitiyTagsScope
{
    const TABLE = 'activity_tags';

    private $tagIds = [];

    public function __construct(array $tagIds)
    {
        $this->tagIds = $tagIds;
    }

    public function __invoke(QueryBuilder $qb)
    {
        $withActivities = $this->scopeTags(
            $qb->getConnection(),
            array_filter($this->tagIds, '\Dime\Server\Functionial\positive')
        );
        $withoutActivities = $this->scopeTags(
            $qb->getConnection(),
            array_filter($this->tagIds, '\Dime\Server\Functionial\negative')
        );        
        return $qb
                ->andWhere('id IN (:id_list)')
                ->setParameter('id_list', array_diff($withActivities, $withoutActivities), Connection::PARAM_INT_ARRAY);
    }

    public function scopeTags(Connection $connection, array $values)
    {
        $result = [];

        if (!empty($values)) {
            $result = $connection->createQueryBuilder()
                    ->from(self::TABLE)
                    ->select('DISTINCT activity_id')
                    ->where('tag_id IN (:tag_id)')
                    ->setParameter('tag_id', array_map('\Dime\Server\Functionial\abs', $values), Connection::PARAM_INT_ARRAY)
                    ->execute()
                    ->fetchAll(PDO::FETCH_COLUMN);
        }

        return $result;
    }
}
