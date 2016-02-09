<?php

namespace Dime\Server\Entity;

use DateTime;
use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation AS JMS;

/**
 * @ORM\Entity
 * @ORM\Table(name="timeslices")
 */
class Timeslice
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $duration;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    protected $startedAt;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    protected $stoppedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Activity")
     */
    protected $activity;

    /**
     * @ORM\ManytoOne(targetEntity="User")
     * @JMS\Exclude
     */
    protected $user;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    protected $updatedAt;

}
