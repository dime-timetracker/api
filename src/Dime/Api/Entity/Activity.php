<?php

namespace Dime\Api\Entity;

use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation as JMS;
use Dime\Server\Behaviors\Assignable;
use Dime\Server\Behaviors\Timestampable;

/**
 * @ORM\Entity
 * @ORM\Table(name="activities")
 */
class Activity implements Assignable, Timestampable
{
    use \Dime\Server\Entity\IdentityEntityTrait;
    use \Dime\Server\Entity\UserEntityTrait;

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
     * @ORM\Column(type="string", nullable=true)
     * @JMS\Type("string")
     */
    protected $rateReference;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Type("integer")
     */
    protected $customerId;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Type("integer")
     */
    protected $projectId;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Type("integer")
     */
    protected $serviceId;

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

    public function getRateReference()
    {
        return $this->rateReference;
    }

    public function setRateReference($rateReference)
    {
        $this->rateReference = $rateReference;
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

    public function getProjectId()
    {
        return $this->projectId;
    }

    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
        return $this;
    }

    public function getServiceId()
    {
        return $this->serviceId;
    }

    public function setServiceId($serviceId)
    {
        $this->serviceId = $serviceId;
        return $this;
    }    
}
