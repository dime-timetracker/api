<?php

namespace Dime\Server\Metadata;

use Dime\Server\Stream\Stream;
use Doctrine\DBAL\Schema\AbstractSchemaManager;

class Metadata
{

    private $schemaManager;

    public function __construct(AbstractSchemaManager $schemaManager)
    {
        $this->schemaManager = $schemaManager;
    }

    public static function with(AbstractSchemaManager $schemaManager)
    {
        return new self($schemaManager);
    }

    public function resources()
    {
        return $this->schemaManager->listTableNames();
    }
    
    public function resource($name)
    {
        return $this->schemaManager->listTableDetails($name);
    }

    public function hasResource($name)
    {
        return $this->schemaManager->tablesExist($name);
    }
    
    public function getPrimaryKey($name)
    {
        return $this->schemaManager->listTableDetails($name)->getPrimaryKey();
    }
    
    /**
     * Iterate thrue data array and filter non existing columns.
     * 
     * @param string $name resource name.
     * @param array $data
     * @return Stream
     */
    public function filter($name, array $data)
    {
        $columns = $this->schemaManager->listTableColumns($name);
        return Stream::of($data)->filter(function ($value, $key) use ($columns) {
            return array_key_exists($key, $columns);
        });
    }
    
    /**
     * Return a stream with types.
     * 
     * @param string $name resource name.
     * @param array $data
     * @return Stream
     */
    public function parameterTypes($name, array $data)
    {
        $columns = $this->schemaManager->listTableColumns($name);
        return $this->filter($name, $data)->map(function ($value, $key) use ($columns) {
            return $columns[$key]->getType();
        });
    }
    
    
}
