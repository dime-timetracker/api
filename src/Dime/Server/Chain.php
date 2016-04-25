<?php

namespace Dime\Server;

class Chain
{
    private $data;
    private $runnables = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public static function it($data)
    {
        return new self($data);
    }

    /**
     *
     * @param type $function
     * @param type $name
     * @return Chain
     */
    public function with($function, $name = null)
    {
        $callName = $name;
        if (is_callable($function, true, $callName)) {
            if (empty($name)) {
                $name = $callName;
            }
            $this->runnables[$name] = $function;
        }
        return $this;
    }

    /**
     *
     * @return mixed
     */
    public function andRun()
    {
        foreach ($this->runnables as $runnable) {
            $this->data = call_user_func($runnable, $this->data);
        }

        return $this->data;
    }
}
