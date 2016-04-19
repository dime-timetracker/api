<?php

namespace Dime\Server\Behavior;

class Timestampable
{
    private $createdAt = 'created_at';
    private $updatedAt = 'updated_at';

    public function __construct($createdAt = 'created_at', $updatedAt = 'updated_at')
    {
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function __invoke(array $data)
    {
        $now = date('Y-m-d H:i:s');
        
        if (!empty($this->createdAt) && !isset($data[$this->createdAt])) {
            $data[$this->createdAt] = $now;
        }
        
        if (!empty($this->updatedAt)) {
            $data[$this->updatedAt] = $now;
        }
        
        return $data;
    }
}
