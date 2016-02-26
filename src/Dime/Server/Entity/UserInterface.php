<?php

namespace Dime\Server\Entity;

interface UserInterface
{
    public function getPassword();

    public function getSalt();
    
}
