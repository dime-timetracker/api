<?php

namespace Dime\Server\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity(repositoryClass="Dime\Server\Entity\AccessRepository")
 * @ORM\Table(name="access")
 */
class Access
{

    use \Dime\Server\Entity\TimestampableEntityTrait;
    
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
