<?php

namespace Dime\Api\Entity;

use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation AS JMS;
use Dime\Server\Behaviors\Assignable;
use Dime\Server\Behaviors\Timestampable;

/**
 * @ORM\Entity
 * @ORM\Table(name="tags")
 */
class Tag implements Assignable, Timestampable
{
    
    use \Dime\Server\Entity\IdentityEntityTrait;
    use \Dime\Server\Entity\UserEntityTrait;

    /**
     * @ORM\Column(type="string", unique=true)
     * @JMS\Type("string")
     */
    protected $name;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     */
    protected $enabled = true;
    
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }



}
