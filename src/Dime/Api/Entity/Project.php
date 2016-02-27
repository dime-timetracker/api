<?php

namespace Dime\Api\Entity;

use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation AS JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Dime\Server\Behaviors\Assignable;

/**
 * @ORM\Entity(repositoryClass="Dime\Api\Entity\ProjectRepository")
 * @ORM\Table(name="projects")
 * @Assert\UniqueEntity({"alias", "userId"})
 */
class Project implements Assignable
{
    use \Dime\Server\Entity\IdentityEntityTrait;
    use \Dime\Security\Entity\UserEntityTrait;
    use \Dime\Server\Entity\TimestampableEntityTrait;

    /**
     * @ORM\Column(type="string")
     * @JMS\Type("string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     * @JMS\Type("string")
     * @Assert\NotBlank
     */
    protected $alias;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @JMS\Type("string")
     */
    protected $description;

    /**
     * @ORM\Column(type="decimal", nullable=true)
     * @JMS\Type("double")
     */
    protected $rate;

    /**
     * @ORM\Column(type="decimal", nullable=true)
     * @JMS\Type("double")
     */
    protected $budgetPrice;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Type("integer")
     */
    protected $budgetTime;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     */
    protected $isBudgetFixed = true;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     */
    protected $enabled = true;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Type("integer")
     */
    protected $customerId;

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

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
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

    public function getBudgetPrice()
    {
        return $this->budgetPrice;
    }

    public function setBudgetPrice($budgetPrice)
    {
        $this->budgetPrice = $budgetPrice;
        return $this;
    }

    public function getBudgetTime()
    {
        return $this->budgetTime;
    }

    public function setBudgetTime($budgetTime)
    {
        $this->budgetTime = $budgetTime;
        return $this;
    }

    public function getIsBudgetFixed()
    {
        return $this->isBudgetFixed;
    }

    public function setIsBudgetFixed($isBudgetFixed)
    {
        $this->isBudgetFixed = $isBudgetFixed;
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

    public function getCustomerId()
    {
        return $this->customerId;
    }

    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
        return $this;
    }

}
