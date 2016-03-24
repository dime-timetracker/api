<?php

namespace Dime\Api\Entity;

use DateTime;
use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation AS JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Dime\Security\Behaviors\Assignable;

/**
 * @ORM\Entity(repositoryClass="Dime\Api\Entity\TimesliceRepository")
 * @ORM\Table(name="timeslices")
 */
class Timeslice implements Assignable
{

    use \Dime\Server\Entity\IdentityEntityTrait;
    use \Dime\Security\Entity\UserEntityTrait;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Type("integer")
     * @JMS\ReadOnly
     */
    protected $duration = 0;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Type("DateTime<'U'>")
     * @var DateTime
     */
    protected $startedAt;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Type("DateTime<'U'>")
     * @var DateTime
     */
    protected $stoppedAt;
    
    /**
     * @ORM\Column(type="integer")
     * @JMS\Type("integer")
     */
    protected $activityId;

    /**
     * @ORM\ManyToOne(targetEntity="Activity", inversedBy="timeslices")
     * @ORM\JoinColumn(name="activity_id", referencedColumnName="id")
     * @JMS\Exclude
     * @var Activity
     */
    protected $activity;

    public function __construct()
    {
        $this->startedAt = new DateTime();
    }

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

    public function getActivity()
    {
        return $this->activity;
    }

    public function setActivity(Activity $activity)
    {
        $this->activity = $activity;
        return $this;
    }

    public function calculateDuration()
    {
        $end = $this->getStoppedAt();
        if ($end == null) {
            $end = new DateTime();
        }

        return $end->getTimestamp() - $this->getStartedAt()->getTimestamp();
    }
}
