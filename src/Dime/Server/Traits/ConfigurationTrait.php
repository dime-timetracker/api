<?php

namespace Dime\Server\Traits;

trait ConfigurationTrait
{
    /**
     * @var array
     */
    protected $config = [];

    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }

    public function getConfigValue(array $path, $default = null)
    {
        $result = $default;

        $level = count($path);
        $data = $this->config;
        foreach ($path as $name) {
            if (isset($data[$name])) {
                $data = $data[$name];
                $level--;
            }
        }

        if ($level === 0) {
            $result = $data;
        }

        return $result;
    }
}