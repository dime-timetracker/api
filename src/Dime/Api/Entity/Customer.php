<?php

namespace Dime\Api\Entity;

use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation AS JMS;
use Dime\Server\Behaviors\Assignable;

/**
 * @ORM\Entity(repositoryClass="Dime\Api\Entity\CustomerRepository")
 * @ORM\Table(name="customers")
 */
class Customer implements Assignable
{
    use \Dime\Server\Entity\IdentityEntityTrait;
    use \Dime\Server\Entity\UserEntityTrait;
    use \Dime\Server\Entity\TimestampableEntityTrait;

    /**
     * @ORM\Column(type="string")
     * @JMS\Type("string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", unique=true)
     * @JMS\Type("string")
     */
    protected $alias;

    /**
     * @ORM\Column(type="decimal", nullable=true)
     * @JMS\Type("double")
     */
    protected $rate;

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
