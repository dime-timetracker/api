<?php

namespace Dime\Server\Entity;

use Dime\Server\Metadata\Metadata;
use Doctrine\DBAL\Connection;

class ResourceRepository
{

    private $connection;
    private $metadata;
    private $name;

    public function __construct(Connection $connection, $name = null)
    {
        $this->connection = $connection;

        if ($this->connection != null) {
            $this->metadata = Metadata::with($this->connection->getSchemaManager());
        }

        if (!empty($name)) {
            $this->name = $name;
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function getMetadata()
    {
        return $this->metadata;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function find($identifier)
    {
        $qb = $this->getConnection()->createQueryBuilder()->select("*")->from($this->getName());
        $qb->where($qb->expr()->eq('id', ':id'))->setParameter('id', $identifier['id']);
        return $qb->execute()->fetch();
    }

    public function findAll(array $filter = [], $page = 1, $with = 0)
    {
        $qb = $this->getConnection()->createQueryBuilder()->select("*")->from($this->getName());

        // Filter
        foreach ($filter as $function) {
            call_user_func($function, $qb);
        }

        // Pager
        $qb->setFirstResult($with * ($page - 1));
        if ($with > 0) {
            $qb->setMaxResults($with);
        }

        return $qb->execute()->fetchAll();
    }

    public function insert(array $data)
    {
        $this->getConnection()->insert(
            $this->getName(), 
            $this->getMetadata()->filter($this->getName(), $data)->collect()
        );

        return $this->getConnection()->lastInsertId();
    }

    public function update(array $data, array $identifier)
    {
        return $this->getConnection()->update(
            $this->getName(), 
            $this->getMetadata()->filter($this->getName(), $data)->collect(), 
            $identifier
        );
    }

    public function delete(array $identifier)
    {
        return $this->connection->delete($this->getName(), $identifier);
    }

}
