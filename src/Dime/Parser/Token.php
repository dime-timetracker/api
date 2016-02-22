<?php

namespace Dime\Parser;

class Token
{
    private $original;

    private $value;

    private $result;

    public function __construct($original) {
        $this->original = $original;
        $this->setValue($original);
    }

    public function getOriginal() {
        return $this->original;
    }

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    public function getResult() {
        return $this->result;
    }

    public function setResult($result) {
        $this->result = $result;
        return $this;
    }

}