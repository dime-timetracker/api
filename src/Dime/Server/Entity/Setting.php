<?php

namespace Dime\Server\Entity;

use DateTime;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="settings")
 */
class Setting
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
   * @ORM\Column(type="string")
   */
  protected $namespace;
  
  /**
   * @ORM\Column(type="string")
   */
  protected $value;

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
