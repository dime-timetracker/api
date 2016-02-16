<?php

namespace Dime\Server\Entity;

use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="services")
 * @ORM\Entity
 */
class Service
{

    use IdentityEntityTrait;
    use UserEntityTrait;

    /**
     * @ORM\Column(type="string")
     * @JMS\Type("string")
     * @var string 
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     * @JMS\Type("string")
     * @var string 
     */
    protected $alias;

    /**
     * @ORM\Column(type="decimal", nullable=true)
     * @JMS\Type("double")
     * @var double
     */
    protected $rate;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     * @var boolean
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

    public function getAlias()
    {
        return $this->alias;
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    public function getRate()
    {
        return $this->rate;
    }

    public function setRate($rate)
    {
        $this->rate = $rate;
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
