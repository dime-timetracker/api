<?php

namespace Dime\Server\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Dime\Server\Entity\AccessRepository")
 * @ORM\Table(name="access")
 */
class Access
{
    use \Dime\Server\Entity\TimestampableEntityTrait;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @var User
     */
    protected $userId;

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     * @var string 
     */
    protected $client;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     * @var string 
     */
    protected $token;

    public function __construct($userId, $client)
    {
        $this->userId = $userId;
        $this->client = $client;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
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
        return date('Y-m-d H:i:s', strtotime($period, $this->getUpdatedAt()->getTimestamp()));
    }

}
