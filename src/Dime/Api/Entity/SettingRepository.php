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
        return $this;
    }
}
