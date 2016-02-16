<?php

namespace Dime\Server\Entity;

use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation AS JMS;

/**
 * @ORM\Entity
 * @ORM\Table(name="tags")
 */
class Tag
{
    
    use IdentityEntityTrait;
    use UserEntityTrait;

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
