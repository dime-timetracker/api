<?php

namespace Dime\Server\Db;

use Dime\Server\Stream;
use Doctrine\DBAL\Query\QueryBuilder;

class Query
{
    private $queryBuilder;

    public static function of(QueryBuilder $queryBuilder)
    {
        return new self($queryBuilder);
    }

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function __invoke()
    {
        return $this->queryBuilder;
    }

    public function __toString()
    {
        return sprintf("Query[%s]", $this->queryBuilder);
    }

    public function map(callable $function) : Query
    {
        return static::of(call_user_func($function, $this->queryBuilder));
    }

    public function count()
    {
        return $this()->select('count(*)')->execute()->fetchColumn();
    }

    public function one()
    {
        return $this()->execute()->fetch();
    }

    public function all() : Stream
    {
        return Stream::of($this()->execute()->fetchAll());
    }
}
