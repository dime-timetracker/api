<?php

namespace Dime\Api\Entity;

use Doctrine\ORM\QueryBuilder;
use Dime\Server\Entity\BaseRepository;


class ServiceRepository extends BaseRepository
{
    public function getAlias()
    {
        return "service";
    }

    public function filter(array $parameter, QueryBuilder $queryBuilder = null)
    {
        if (empty($parameter)) {
            return $this;
        }
        
        $qb = $this->getQueryBuilder($queryBuilder);
        foreach ($parameter as $name => $value) {
            switch ($name) {
                case 'search':
                    $qb = $this->search($value, $qb);
                    break;
            }
        }

        return $this;
    }

    public function search($value, QueryBuilder $queryBuilder)
    {
        $qb = $this->getQueryBuilder($queryBuilder);
        $alias = $this->getFirstRootAlias($qb);

        $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like($alias . '.description', ':text_like'),
                $qb->expr()->like($alias . '.name', ':text_like'),
                $qb->expr()->eq($alias . '.alias', ':text')
            ));
        $qb->setParameter('text_like', '%' . $value . '%');
        $qb->setParameter('text', $value);

        return $this;
    }
}
