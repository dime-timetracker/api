<?php

namespace Dime\Api\Entity;

use Doctrine\ORM\QueryBuilder;
use Dime\Server\Entity\BaseRepository;

class TimesliceRepository extends BaseRepository
{
    public function getAlias()
    {
        return "timeslice";
    }

    public function filter(array $parameter, QueryBuilder $queryBuilder = null)
    {
        if (empty($parameter)) {
            return $this;
        }

        $qb = $this->getQueryBuilder($queryBuilder);
        foreach ($parameter as $key => $value) {
            $activity_data = [];
            
            switch($key) {
                case 'date':
                    $this->scopeByDate($value, $qb);
                    break;
                case 'customer':
                    $activity_data['customerId'] = $value;
                    break;
                case 'project':
                    $activity_data['projectId'] = $value;
                    break;
                case 'service':
                    $activity_data['serviceId'] = $value;
                    break;
                case 'tags':
                    $this->scopeByTags($value, $qb);
                    break;
            }

            if (!empty($activity_data)) {
                $this->scopeByActivityData($activity_data, $qb);
            }
        }

        return $this;
    }

    public function scopeByDate($date, QueryBuilder $queryBuilder = null)
    {
        $qb = $this->getQueryBuilder($queryBuilder);
        $alias = $this->getFirstRootAlias($qb);

        if (is_array($date)) {
            $qb->andWhere(
                $qb->expr()->between($alias . '.startedAt', ':from', ':to')
            );
            $qb->setParameter('from', $date[0] . ' 00:00:00');
            $qb->setParameter('to', $date[1] . ' 23:59:59');
        } else {
            $qb->andWhere(
                $qb->expr()->like($alias . '.startedAt', ':date')
            );
            $qb->setParameter('date', $date . '%');
        }

        return $this;
    }

    public function scopeByActivityData(array $data, QueryBuilder $queryBuilder = null)
    {
        $qb = $this->getQueryBuilder($queryBuilder);
        $alias = $this->getFirstRootAlias($qb);

        if (!empty($data)) {
            if (!$this->existsJoinAlias($qb, 'a')) {
                $qb->leftJoin($alias . '.activity', 'a');
            }

            foreach ($data as $field => $value) {
                $qb->andWhere($qb->expr()->eq("a." . $field, ":" . $field));
                $qb->setParameter(":" . $field, $value);
            }
        }

        return $this;
    }

    public function scopeByTags($tags, QueryBuilder $queryBuilder = null)
    {
        return $this;
    }
}
