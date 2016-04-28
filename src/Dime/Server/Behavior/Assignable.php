<?php

namespace Dime\Server\Behavior;

class Assignable
{
    private $userId;
    
    public function __construct($userId)
    {
        $this->userId = intval($userId);
    }

    public function __invoke(array $data = [])
    {        
        $data['user_id'] = $this->userId;
        return $data;
    }
}
