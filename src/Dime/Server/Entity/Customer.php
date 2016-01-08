<?php

namespace Dime\Server\Entity;

use DateTime;
use JMS\Serializer\Annotation\Exclude;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="customers")
 */
class Customer
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
  protected $name;
  
  /**
   * @ORM\Column(type="string", unique=true)
   */
  protected $alias;

  /**
   * @ORM\Column(type="decimal")
   */
  protected $rate;

  /**
   * @ORM\Column(type="boolean")
   */
  protected $enabled = true;

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
