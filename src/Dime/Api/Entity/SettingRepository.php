<?php

namespace Dime\Api\Entity;

use Doctrine\ORM\QueryBuilder;
use Dime\Server\Entity\BaseRepository;

class SettingRepository extends BaseRepository
{
    public function getAlias()
    {
        return "setting";
    }

    public function filter(array $parameter, QueryBuilder $queryBuilder = null)
    {
        if (empty($parameter)) {
            return $this;
        }

        $qb = $this->getQueryBuilder($queryBuilder);
        foreach ($parameter as $key => $value) {
            switch($key) {
                case 'name':
                    $this->scopeByField('name', $value, $qb);
                    break;
                case 'search':
                    $this->search($value, $qb);
                    break;
            }
        }

        return $this;
    }

    public function search($text, QueryBuilder $queryBuilder = null)
    {
        $qb = $this->getQueryBuilder($queryBuilder);
        $alias = $this->getFirstRootAlias($qb);

        $qb->andWhere(
            $qb->expr()->like($alias . '.name', ':text_like')
        );
        $qb->setParameter('text_like', '%' . $text . '%');
        
        return $this;
    }
}
