<?php

namespace Dime\Server\Scope;

use Doctrine\DBAL\Query\QueryBuilder;

class WithUser
{

    private $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function __invoke(QueryBuilder $qb)
    {
        return $qb->andWhere($qb->expr()->eq('user_id', ':user_id'))->setParameter('user_id', $this->userId);
    }

}
