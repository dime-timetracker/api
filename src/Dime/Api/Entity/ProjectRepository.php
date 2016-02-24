<?php

namespace Dime\Api\Entity;

use Doctrine\ORM\QueryBuilder;
use Dime\Server\Entity\BaseRepository;

class ProjectRepository extends BaseRepository
{
    public function getAlias()
    {
        return "project";
    }

    public function filter(array $parameter, QueryBuilder $queryBuilder = null)
    {
        return $this;
    }
}
