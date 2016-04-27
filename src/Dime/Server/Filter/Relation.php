<?php

namespace Dime\Server\Filter;

class Relation implements FilterInterface
{
    private $name;
    private $mappedTo;

    public function __construct($name, $mappedTo = null)
    {
        $this->name = $name;

        if (empty($mappedTo)) {
            $this->mappedTo = $this->name . '_id';
        }
    }

    public function name()
    {
        return $this->name;
    }

    public function __invoke($value)
    {
       // TODO Sanetize or Arrayize
       return new \Dime\Server\Scope\With([ $this->mappedTo => $value ]);
    }
}
