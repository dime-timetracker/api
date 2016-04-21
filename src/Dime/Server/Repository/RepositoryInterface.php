<?php

namespace Dime\Server\Repository;

interface RepositoryInterface
{
    public function find(array $identifier);

    public function findAll(array $filter = [], $page = 1, $with = 0);

    public function insert(array $data);
    
    public function update(array $data, array $identifier);
    
    public function delete(array $identifier);
}
