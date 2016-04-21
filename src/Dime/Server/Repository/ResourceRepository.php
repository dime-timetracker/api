<?php

namespace Dime\Server\Repository;

use Dime\Server\Metadata;
use Doctrine\DBAL\Connection;

class ResourceRepository implements RepositoryInterface
{

    private $connection;
    private $metadata;
    private $name;

    /**
     * Constructor.
     * @param Connection $connection Database connection
     * @param string $name Resource name.
     */
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

    /**
     * The database connection.
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Get table metadata.
     * @return Metadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @return string name of resource
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Resource name.
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Find one entity.
     * @param array $identifier
     * @return array
     */
    public function find(array $identifier)
    {
        $qb = $this->getConnection()->createQueryBuilder()->select("*")->from($this->getName());
        
        foreach ($identifier as $key => $value) {
            $qb->where($qb->expr()->eq($key, ':' . $key))->setParameter($key, $value);
        }
        
        return $qb->execute()->fetch();
    }

    /**
     * Find all entities.
     * @param array $filter array with callables getting QueryBuilder as parameter.
     * @param int $page page number (default: 1)
     * @param int $with amount of entity (default: 0)
     * @return array
     */
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

    /**
     * Insert entity.
     * @param array $data
     * @return int id of the last inserted.
     */
    public function insert(array $data)
    {
        $this->getConnection()->insert(
            $this->getName(), 
            $this->getMetadata()->filter($this->getName(), $data)->collect()
        );

        return $this->getConnection()->lastInsertId();
    }

    /**
     * Update entity.
     * @param array $data
     * @param array $identifier
     * @return int amount of affected rows
     */
    public function update(array $data, array $identifier)
    {
        return $this->getConnection()->update(
            $this->getName(), 
            $this->getMetadata()->filter($this->getName(), $data)->collect(), 
            $identifier
        );
    }

    /**
     * Delete entity.
     * @param array $identifier
     * @return int amount of affected rows
     */
    public function delete(array $identifier)
    {
        return $this->connection->delete($this->getName(), $identifier);
    }

}
