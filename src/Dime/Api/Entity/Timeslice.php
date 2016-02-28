<?php

namespace Dime\Api\Entity;

use DateTime;
use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation AS JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Dime\Api\Behaviors\Assignable;

/**
 * @ORM\Entity(repositoryClass="Dime\Api\Entity\TimesliceRepository")
 * @ORM\Table(name="timeslices")
 */
class Timeslice implements Assignable
{

    use \Dime\Server\Entity\IdentityEntityTrait;
    use \Dime\Security\Entity\UserEntityTrait;
    use \Dime\Server\Entity\TimestampableEntityTrait;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Type("integer")
     */
    protected $duration;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Type("DateTime<'Y-m-d H:i:s'>")
     * @var DateTime
     */
    protected $startedAt;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Type("DateTime<'Y-m-d H:i:s'>")
     * @var DateTime
     */
    protected $stoppedAt;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Type("integer")
     * @Assert\NotBlank
     */
    protected $activityId;

    public function getDuration()
    {
        return $this->duration;
    }

    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }

    public function getStartedAt()
    {
        return $this->startedAt;
    }

    public function setStartedAt(DateTime $startedAt)
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function getStoppedAt()
    {
        return $this->stoppedAt;
    }

    public function setStoppedAt(DateTime $stoppedAt)
    {
        $this->stoppedAt = $stoppedAt;
        return $this;
    }

    public function getActivityId()
    {
        return $this->activityId;
    }

    public function setActivityId($activityId)
    {
        $this->activityId = $activityId;
        return $this;
    }

}
