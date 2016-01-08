<?php

namespace Dime\Server\Entity;

use DateTime;
use JMS\Serializer\Annotation\Exclude;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="activities")
 */
class Activity 
{

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue
   */
  protected $id;

  /**
   * @ORM\Column(type="string")
   */
  protected $description;

  /**
   * @ORM\Column(type="decimal")
   */
  protected $rate;

  /**
   * @ORM\Column(type="string")
   */
  protected $rateReference;

  /**
   * @ORM\ManyToOne(targetEntity="Customer")
   */
  //protected $customer;

  /**
   * @ORM\ManyToOne(targetEntity="Project")
   */
//  protected $project;

  /**
   * @ORM\ManyToOne(targetEntity="Service")
   */
  protected $service;

  /**
   * @ORM\ManyToOne(targetEntity="User")
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
