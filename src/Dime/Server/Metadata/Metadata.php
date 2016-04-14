<?php

namespace Dime\Server\Metadata;

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
    
    public function hasResource($name)
    {
        return $this->schemaManager->tablesExist($name);
    }
    
}
