<?php

namespace Dime\Server\Scope;

use Doctrine\DBAL\Query\QueryBuilder;

class With
{
    private $identifier;

    public function __construct(array $identifier = [])
    {
        $this->identifier = $identifier;
    }

    public function __invoke(QueryBuilder $qb)
    {
        foreach ($this->identifier as $key => $value) {
            $qb = $qb->andWhere(
                $qb->expr()->eq($key, ':' . $key)
            )->setParameter($key, $value);
        }

        return $qb;
    }
}
