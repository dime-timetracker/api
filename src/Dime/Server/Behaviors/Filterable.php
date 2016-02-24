<?php

namespace Dime\Server\Behaviors;
use Doctrine\ORM\QueryBuilder;

interface Filterable
{
    public function filter(array $parameter, QueryBuilder $queryBuilder = null);
}
