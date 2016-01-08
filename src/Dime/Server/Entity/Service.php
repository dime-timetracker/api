<?php

namespace Dime\Server\Entity;

use DateTime;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="services")
 */
class Service {

    /**
     * @ORM\Id 
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @var string 
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     * @var string 
     */
    protected $alias;

    /**
     * @ORM\Column(type="decimal")
     * @var double
     */
    protected $rate;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    protected $enabled = true;

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

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getAlias() {
        return $this->alias;
    }

    public function getRate() {
        return $this->rate;
    }

    public function getEnabled() {
        return $this->enabled;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setAlias($alias) {
        $this->alias = $alias;
        return $this;
    }

    public function setRate($rate) {
        $this->rate = $rate;
        return $this;
    }

    public function setEnabled($enabled) {
        $this->enabled = $enabled;
        return $this;
    }

    public function setCreatedAt(DateTime $createdAt) {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function setUpdatedAt(DateTime $updatedAt) {
        $this->updatedAt = $updatedAt;
        return $this;
    }

}
