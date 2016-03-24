<?php

namespace Dime\Security\Behaviors;

interface Assignable
{
    public function getUserId();

    public function setUserId($userId);
}
