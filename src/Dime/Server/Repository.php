<?php

namespace Dime\Server;

use Dime\Server\Metadata;
use Doctrine\DBAL\Connection;

class Repository
{

    private $connection;
    private $metadata;
    private $table;

    /**
     * Constructor.
     * @param Connection $connection Database connection
     * @param string $table Name of the table.
     */
    public function __construct(Connection $connection, $table = null)
    {
        $this->connection = $connection;

        if ($this->connection != null) {
            $this->metadata = new Metadata($this->connection->getSchemaManager());
        }

        if (!empty($table)) {
            $this->table = $table;
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
        return $this->table;
    }

    /**
     * Resource name.
     * @param string $name
     */
    public function setName($name)
    {
        $this->table = $name;
    }

    /**
     * Find one entity.
     * @param array $with array with callables getting QueryBuilder as parameter.
     * @return array
     */
    public function find(array $with = [])
    {
        $qb = $this->getConnection()->createQueryBuilder()->select('*')->from($this->getName());

        foreach ($with as $action) {
            $qb = call_user_func($action, $qb);
        }

        return $qb->execute()->fetch();
    }

    /**
     * Find all entities.
     * @param array $with array with callables getting QueryBuilder as parameter.
     * @return array
     */
    public function findAll(array $with = [])
    {
        $qb = $this->getConnection()->createQueryBuilder()->select('*')->from($this->getName());


        foreach ($with as $action) {
            $qb = call_user_func($action, $qb);
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
        try {
            $this->getConnection()->insert(
                $this->getName(),
                $this->getMetadata()->filter($this->getName(), $data)->collect()
            );
        } catch (\Exception $e) {
            throw new RepositoryException('No data', $e->getCode(), $e);
        }

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

    /**
     * Count all entities.
     * @return int
     */
    public function count()
    {
        $qb = $this->getConnection()->createQueryBuilder()->select('count(*)')->from($this->getName());
        return $qb->execute()->fetchColumn();
    }
}
