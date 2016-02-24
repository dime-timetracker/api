<?php

namespace Dime\Server\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity(repositoryClass="Dime\Server\Entity\UserRepository")
 * @ORM\Table(name="users")
 */
class User
{

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
    protected $username;

    /**
     * @ORM\Column(type="string")
     * @var string 
     */
    protected $password;

    /**
     * @ORM\Column(type="string")
     * @var string 
     */
    protected $salt;

    /**
     * @ORM\Column(type="string")
     * @var string 
     */
    protected $email;

    /**
     * @ORM\Column(type="string")
     * @var string 
     */
    protected $firstname;

    /**
     * @ORM\Column(type="string")
     * @var string 
     */
    protected $lastname;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean 
     */
    protected $enabled = true;

    /**
     * @ORM\OneToMany(targetEntity="Access", mappedBy="user") 
     */
    private $access;

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function getAccess()
    {
        return $this->access;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function setAccess($access)
    {
        $this->access = $access;
        return $this;
    }

}
