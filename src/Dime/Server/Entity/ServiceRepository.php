<?php

namespace Dime\Server\Entity;

use Dime\Behaviors\Filterable;
use Doctrine\ORM\EntityRepository;


class ServiceRepository extends EntityRepository implements Filterable {

    public function filter(array $parameter)
    {
    }
}
