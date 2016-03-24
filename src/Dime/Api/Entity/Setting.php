<?php

namespace Dime\Api\Entity;

use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation AS JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Dime\Security\Behaviors\Assignable;

/**
 * @ORM\Entity(repositoryClass="Dime\Api\Entity\SettingRepository")
 * @ORM\Table(name="settings")
 */
class Setting implements Assignable
{
    
    use \Dime\Server\Entity\IdentityEntityTrait;
    use \Dime\Security\Entity\UserEntityTrait;
    use \Dime\Server\Entity\TimestampableEntityTrait;

    /**
     * @ORM\Column(type="string")
     * @JMS\Type("string")
     * @Assert\NotBlank
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @JMS\Type("string")
     */
    protected $value;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

}
