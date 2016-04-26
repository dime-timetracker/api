<?php

namespace Dime\Server\Scope;

use Doctrine\DBAL\Query\QueryBuilder;

class WithIdentity
{
    private $identifier;

    public function __construct(array $identifier = [])
    {
        $this->identifier = $identifier;
    }

    public function __invoke(QueryBuilder $qb)
    {
        foreach ($this->identifier as $key => $value) {
            $qb = $qb->where(
                $qb->expr()->eq($key, ':' . $key)
            )->setParameter($key, $value);
        }

        return $qb;
    }
}
