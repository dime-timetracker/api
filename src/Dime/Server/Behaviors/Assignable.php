<?php

namespace Dime\Server\Behaviors;

interface Assignable
{
    public function getUserId();

    public function setUserId($userId);
}
