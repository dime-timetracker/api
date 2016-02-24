<?php

namespace Dime\Server\Entity;

use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation as JMS;

trait UserEntityTrait
{    
    /**
     * @ORM\Column(type="integer")
     * @JMS\Type("integer")
     * @JMS\Exclude
     */
    protected $userId;
    
    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

}