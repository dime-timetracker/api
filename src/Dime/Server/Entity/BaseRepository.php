<?php

namespace Dime\Server\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Dime\Server\Behaviors\Filterable;

abstract class BaseRepository extends EntityRepository implements Filterable
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    abstract public function getAlias();

    public function getQueryBuilder(QueryBuilder $queryBuilder = null)
    {
        if ($queryBuilder != null) {
            $this->setQueryBuilder($queryBuilder);
        }
        
        if ($this->queryBuilder == null) {
            $this->setQueryBuilder($this->createQueryBuilder($this->getAlias()));
        }
        return $this->queryBuilder;
    }

    public function setQueryBuilder(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        return $this;
    }

    public function getFirstRootAlias(QueryBuilder $queryBuilder)
    {
        $aliases = $queryBuilder->getRootAliases();
        return array_shift($aliases);
    }

    /**
     * Scope by any field with value
     *
     * @param $field
     * @param $value
     * @param QueryBuilder $qb
     * @return EntityRepository
     */
    public function scopeByField($field, $value, QueryBuilder $queryBuilder = null)
    {
        $qb = $this->getQueryBuilder($queryBuilder);
        $alias = $this->getFirstRootAlias($qb);

        $qb->andWhere(
            $qb->expr()->eq($alias . '.' . $field, ':' . $field)
        );
        $qb->setParameter($field, $value);

        return $this;
    }

}
