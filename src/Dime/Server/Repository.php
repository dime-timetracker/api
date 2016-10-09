<?php

namespace Dime\Server;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Dime\Server\Db\Query;

class Repository
{

    private $connection;
    private $table;

    /**
     * Constructor.
     * @param Connection $connection Database connection
     * @param string $table Name of the table.
     */
    public function __construct(Connection $connection, $table = null)
    {
        $this->connection = $connection;
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

    public function getSchemaManager()
    {
        return $this->getConnection()->getSchemaManager();
    }

    /**
     * The alias are the first three letters of tablename. Example user -> use
     * @return string
     */
    public function getAlias()
    {
        return substr($this->table, 0, 4);
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
    public function setName($name) : Repository
    {
        $this->table = $name;

        return $this;
    }

    /**
     * Generate a new query builder.
     * @return Query
     */
    public function query() : Query
    {
        return Query::of($this->getConnection()
            ->createQueryBuilder()
            ->from($this->getName())
            ->select('*')
        );
    }

    /**
     * Find one entity.
     * @param array $scopes array with callables getting QueryBuilder as parameter.
     * @return array
     */
    public function find(array $identifier)
    {
        return $this->query()->map(new \Dime\Server\Scope\WithScope($identifier))->one();
    }

    /**
     * Find all entities.
     * @param array $scopes array with callables getting QueryBuilder as parameter.
     * @return array
     */
    public function findAll(array $scopes = [])
    {
        return $this->scopedQuery($scopes)->execute()->fetchAll();
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
                $this->filter($this->getName(), $data)->collect()
            );
        } catch (\Exception $e) {
            throw new \Exception('No data', $e->getCode(), $e);
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
            $this->filter($this->getName(), $data)->collect(),
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
    public function count(array $scopes = [])
    {
        return $this->scopedQuery($scopes)->select('count(*)')->execute()->fetchColumn();
    }

    /**
     * Create a scope query builder.
     *
     * @param array $scopes
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    protected function scopedQuery(array $scopes = [], QueryBuilder $qb = null)
    {
        if ($qb == null) {
            $qb = $this
                ->getConnection()
                ->createQueryBuilder()
                ->from($this->getName(), $this->getAlias())
                ->select('*');
        }

        foreach ($scopes as $action) {
            if (is_callable($action, true)) {
                $qb = call_user_func($action, $qb);
            }
        }

        return $qb;
    }

    protected function filter($name, array $data)
    {
        $columns = $this->getSchemaManager()->listTableColumns($name);
        return Stream::of($data)->filter(function ($value, $key) use ($columns) {
            return array_key_exists($key, $columns);
        });
    }
}
