<?php

namespace Dime\Api\Entity;

use Doctrine\ORM\QueryBuilder;
use Dime\Server\Entity\BaseRepository;

class ActivityRepository extends BaseRepository
{

    public function getAlias()
    {
        return "activity";
    }

    public function filter(array $parameter, QueryBuilder $queryBuilder = null)
    {
        if (empty($parameter)) {
            return $this;
        }
        
        $qb = $this->getQueryBuilder($queryBuilder);
        foreach ($parameter as $key => $value) {
            switch ($key) {
                case 'active':
                    $this->scopeByActive($value, $qb);
                    break;
                case 'customer':
                    $this->scopeByCustomer($value, $qb);
                    break;
                case 'project':
                    $this->scopeByProject($value, $qb);
                    break;
                case 'service':
                    $this->scopeByService($value, $qb);
                    break;
                case 'tags':
                    $this->scopeByTags($value, $qb);
                    break;
                case 'date':
                    $this->scopeByDate($value, $qb);
                    break;
                case 'search':
                    $this->search($value, $qb);
                    break;
            }
        }

        return $this;
    }
    
    public function scopeByCustomer($id, QueryBuilder $queryBuilder = null)
    {
        return $this->scopeByField('customerId', $id, $queryBuilder);
    }

    public function scopeByProject($id, QueryBuilder $queryBuilder = null)
    {
        return $this->scopeByField('projectId', $id, $queryBuilder);
    }

    public function scopeByService($id, QueryBuilder $queryBuilder = null)
    {
        return $this->scopeByField('serviceId', $id, $queryBuilder);
    }

    public function scopeByActive($active, QueryBuilder $queryBuilder = null)
    {
        $qb = $this->getQueryBuilder($queryBuilder);

        $timesliceRepository = $this->getEntityManager()->getRepository('Dime\Api\Entity\Timeslice');
        $ids = $timesliceRepository->fetchRunningActivityIds();

        if ($active == 'true' || (is_bool($active) && $active)) {
            // empty id list, should produce no output on query
            if (empty($ids)) {
                $ids[] = 0;
            }
            $qb->andWhere(
                $qb->expr()->in('a.id', $ids)
            );
        } else {
            if (!empty($ids)) {
                $qb->andWhere(
                    $qb->expr()->notIn('a.id', $ids)
                );
            }
        }

        return $this;
    }

    public function scopeByDate($date, QueryBuilder $queryBuilder = null)
    {
        $qb = $this->getQueryBuilder($queryBuilder);
        $alias = $this->getFirstRootAlias($qb);

        if (is_array($date) && count($date) == 1) {
            $date = array_shift($date);
        }

        if (is_array($date)) {
            $qb->andWhere(
                $qb->expr()->between($alias . '.updatedAt', ':from', ':to')
            );
            $qb->setParameter('from', $date[0]. ' 00:00:00');
            $qb->setParameter('to', $date[1] . ' 23:59:59');
        } else {
            $qb->andWhere(
                $qb->expr()->like($alias . '.updatedAt', ':date')
            );
            $qb->setParameter('date', $date . '%');
        }

        return $this;
    }

    public function search($text, QueryBuilder $queryBuilder = null)
    {
        $qb = $this->getQueryBuilder($queryBuilder);
        $alias = $this->getFirstRootAlias($qb);

        $qb->andWhere($qb->expr()->like($alias . '.description', ':text'));
        $qb->setParameter('text', '%'  . $text . '%');

        return $this;
    }

}
