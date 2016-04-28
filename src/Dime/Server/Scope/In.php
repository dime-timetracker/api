<?php

namespace Dime\Server\Scope;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class In
{
    private $name;
    private $list;

    public function __construct($name, array $list)
    {
        $this->name = $name;
        $this->list = $list;
    }

    public function __invoke(QueryBuilder $qb)
    {
        return $qb
                ->andWhere($this->name . ' IN (:' . $this->name . '_list)')
                ->setParameter($this->name . '_list', $this->list, Connection::PARAM_INT_ARRAY);
    }
}
