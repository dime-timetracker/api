<?php

namespace Dime\Server\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class Login
{
    /**
     * @JMS\Type("string")
     * @Assert\NotBlank
     */
    protected $user;

    /**
     * @JMS\Type("string")
     * @Assert\NotBlank
     */
    protected $password;

    /**
     * @JMS\Type("string")
     * @Assert\NotBlank
     */
    protected $client;

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
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
}
