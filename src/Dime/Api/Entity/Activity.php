<?php

namespace Dime\Api\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;
use Dime\Security\Behaviors\Assignable;

/**
 * @ORM\Entity(repositoryClass="Dime\Api\Entity\ActivityRepository")
 * @ORM\Table(name="activities")
 */
class Activity implements Assignable
{
    use \Dime\Server\Entity\IdentityEntityTrait;
    use \Dime\Security\Entity\UserEntityTrait;
    use \Dime\Server\Entity\TimestampableEntityTrait;

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

    /**
     * @ORM\OneToMany(targetEntity="Timeslice", mappedBy="activity")
     * @JMS\Type("ArrayCollection<Dime\Api\Entity\Timeslice>")
     * @var ArrayCollection
     */
    protected $timeslices;
    
    /**
     * @ORM\ManyToMany(targetEntity="Tag")
     * @ORM\JoinTable(
     *      name="activity_tags",
     *      joinColumns={@ORM\JoinColumn(name="activity_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     * )
     * @JMS\Type("ArrayCollection<Dime\Api\Entity\Tag>")
     * @var ArrayCollection
     */
    protected $tags;

    public function __construct() {
        $this->tags = new ArrayCollection();
        $this->timeslices = new ArrayCollection();
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
    
    public function getTags()
    {
        return $this->tags;
    }
    
    public function setTags(ArrayCollection $tags)
    {
        $this->tags = $tags;
        return $this;
    }

    public function getTimeslices()
    {
        return $this->timeslices;
    }

    public function setTimeslices(ArrayCollection $timeslices)
    {
        $this->timeslices = $timeslices;
        return $this;
    }
}
