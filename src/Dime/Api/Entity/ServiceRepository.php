<?php

namespace Dime\Api\Entity;

use Dime\Server\Behaviors\Filterable;
use Doctrine\ORM\EntityRepository;


class ServiceRepository extends EntityRepository implements Filterable {

    public function filter(array $parameter)
    {
    }
}
