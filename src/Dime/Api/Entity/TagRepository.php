<?php

namespace Dime\Api\Entity;

use Doctrine\ORM\QueryBuilder;
use Dime\Server\Entity\BaseRepository;

class TagRepository extends BaseRepository
{
    public function getAlias()
    {
        return "tag";
    }

    public function filter(array $parameter, QueryBuilder $queryBuilder = null)
    {
        return $this;
    }
}
