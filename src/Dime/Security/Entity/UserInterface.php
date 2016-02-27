<?php

namespace Dime\Security\Entity;

interface UserInterface
{
    public function getPassword();

    public function getSalt();
    
}
