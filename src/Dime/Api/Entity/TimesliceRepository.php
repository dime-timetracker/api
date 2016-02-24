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
        return $this;
    }
}
