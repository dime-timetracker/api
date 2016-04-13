<?php

namespace Dime\Server\Stream;

class Stream
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public static function of($data) {
        return new Streams($data);
    }

    public function map(callable $function)
    {
        $result = [];
        foreach ($this->value as $key => $value) {
            $result[$key] = call_user_func($function, $value);
        }

        return self::of($result);
    }

    public function filter(callable $function)
    {
        $result = [];
        foreach ($this->value as $key => $value) {
            if (call_user_func($function, $value)) {
                $result[$key] = $value;
            }
        }
        return self::of($result);
    }

    public function fold(callable $function, $accumulator = null)
    {
        foreach ($this->value as $key => $value) {
            if (empty($accumulator)) {
                $accumulator = $value;
            } else {
                $accumulator = call_user_func($function, $accumulator, $item);
            }
        }
        return $accumulator;
    }

    function collect()
    {
        return $this->array;
    }
}
