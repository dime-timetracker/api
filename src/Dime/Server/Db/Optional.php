<?php

namespace Dime\Server\Db;

class Optional
{
    private $value;

    public static function of($value)
    {
        return new self($value);
    }

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function orElse($otherValue)
    {
        return $this->isPresent() ? $this->value : $otherValue;
    }

    public function orThrow(\Exception $exception)
    {
        return $this->isPresent() ? $this->value : throw $exception;
    }

    protected function isPresent()
    {
        return $this->value !== NULL;
    }
}
