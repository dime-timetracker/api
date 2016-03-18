<?php

namespace Dime\Server\Traits;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

trait ManagerTrait
{
    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var EntityRepository
     */
    private $repository;

    public function getManager()
    {
        return $this->manager;
    }

    public function setManager(EntityManager $manager)
    {
        $this->manager = $manager;
        return $this;
    }

    /**
     * @param string $name Repository class
     * @return EntityRepository
     */
    public function getRepository($name)
    {
        if ($this->repository == null) {
            $this->repository = $this->getManager()->getRepository($name);
        }
        return $this->repository;
    }

    public function save($entity)
    {
        if (!empty($entity)) {
            $this->getManager()->persist($entity);
            $this->getManager()->flush();
            $this->getManager()->refresh($entity);
        }
        return $entity;
    }
}
