<?php

namespace Dime\Server\Stream;

class Stream
{
    private $data;
    private $traversable = false;

    public function __construct($data)
    {
        $this->data = $data;
        $this->traversable = is_array($this->data) || ($this->data instanceof \Traversable);
    }

    public static function of($data) {
        return new self($data);
    }

    public function collect()
    {
        return $this->data;
    }

    public function execute($function)
    {
        if ($this->traversable) {
            foreach ($this->data as $key => $value) {
                call_user_func($function, $value, $key);
            }
        }

        return $this;
    }

    public function filter($function)
    {
        $result = [];
        if ($this->traversable) {
            foreach ($this->data as $key => $value) {
                if (call_user_func($function, $value, $key)) {
                    $result[$key] = $value;
                }
            }
        }
        return self::of($result);
    }

    public function fold($function, $accumulator = null)
    {
        if ($this->traversable) {
            foreach ($this->data as $key => $value) {
                if (empty($accumulator)) {
                    $accumulator = $value;
                } else {
                    $accumulator = call_user_func($function, $accumulator, $value, $key);
                }
            }
        }
        return $accumulator;
    }

    public function map($function)
    {
        $result = [];

        if ($this->traversable) {
            foreach ($this->data as $key => $value) {
                $result[$key] = call_user_func($function, $value, $key);
            }
        }

        return self::of($result);
    }
}
