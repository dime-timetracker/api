<?php

namespace Dime\Api\Entity;

use Doctrine\ORM\QueryBuilder;
use Dime\Server\Entity\BaseRepository;

class CustomerRepository extends BaseRepository
{
    public function getAlias()
    {
        return "customer";
    }

    public function filter(array $parameter, QueryBuilder $queryBuilder = null)
    {
        if (empty($parameter)) {
            return $this;
        }

        $qb = $this->getQueryBuilder($queryBuilder);
        foreach ($parameter as $key => $value) {
            switch($key) {
                case 'search':
                    $this->search($value, $qb);
                    break;
                case 'tags':
                    $this->scopeByTags($value, $qb);
                    break;
            }
        }

        return $this;
    }

    public function scopeByTag($tags, QueryBuilder $queryBuilder = null)
    {
        $qb = $this->getQueryBuilder($queryBuilder);
        $alias = $this->getFirstRootAlias($qb);

        if (!$this->existsJoinAlias($qb, 'x')) {
            $qb->innerJoin(
                $alias . '.tags',
                'x',
                'WITH',
                is_numeric($tags) ? 'x.id = :tag' : 'x.name = :tag'
            );
        }
        $qb->setParameter('tag', $tags);
        return $this;
    }

    public function search($text, QueryBuilder $queryBuilder = null)
    {
        $qb = $this->getQueryBuilder($queryBuilder);
        $alias = $this->getFirstRootAlias($qb);

        $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like($alias . '.name', ':text_like'),
                $qb->expr()->eq($alias . '.alias', ':text')
            ));
        $qb->setParameter('text_like', '%' . $text . '%');
        $qb->setParameter('text', $text);

        return $this;
    }
}
