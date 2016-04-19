<?php

namespace Dime\Server\Behavior;

class Assignable
{
    private $userId = 'user_id';

    public function __construct($userId = 'user_id')
    {
        $this->userId = $userId;
    }

    public function __invoke(array $data)
    {        
        $data[$this->userId] = 1; // FIXME
        return $data;
    }
}
