<?php

namespace Dime\Api\Behaviors;

interface Assignable
{
    public function getUserId();

    public function setUserId($userId);
}
