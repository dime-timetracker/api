<?php

namespace Dime\Server\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="access")
 */
class Access
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User")
     * @var User
     */
    protected $user;

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     * @var string 
     */
    protected $client;

    /**
     * @ORM\Column(type="string")
     * @var string 
     */
    protected $token;

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

    public function __construct(User $user, $client)
    {
        $this->user = $user;
        $this->client = $client;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setClient($client)
    {
        $this->client = $client;
        return $this;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function setUpdatedAt(DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Return expire date
     * @param string $period
     * @return string
     */
    public function expires($period)
    {
        return date('Y-m-d H:i:s', strtotime($period, strtotime($this->getUpdatedAt())));
    }

}
