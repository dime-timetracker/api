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
        return $this;
    }
}
