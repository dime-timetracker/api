<?php

namespace Dime\Server\Traits;

use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface;

trait ResourceTrait
{
    use \Dime\Server\Traits\DimeResponseTrait;
    
    private $config;
    private $manager;
    private $repository;

    public function __construct(array $config, EntityManager $manager)
    {
        $this->config = $config;
        $this->manager = $manager;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getResourceConfig($resource, $key, $default = null)
    {
        $result = $default;
        $config = $this->getConfig();
        if (isset($config['resources'][$resource]) && isset($config['resources'][$resource][$key])) {
            $result = $config['resources'][$resource][$key];
        }
        return $result;
    }

    /**
     * @return EntityManager
     */
    protected function getManager()
    {
        return $this->manager;
    }

    protected function getRepository($resource)
    {
        if ($this->repository == null) {
            $this->repository = $this->getManager()->getRepository($this->getResourceConfig($resource, 'entity'));
        }
        return $this->repository;
    }

    protected function hasQueryParam(ServerRequestInterface $request, $name)
    {
        $parameters = $request->getQueryParams();
        return !empty($parameters) && isset($parameters[$name]);
    }

    protected function getQueryParam(ServerRequestInterface $request, $name, $default = null)
    {
        $result = $default;
        if ($this->hasQueryParam($request, $name)) {
            $parameters = $request->getQueryParams();
            $result = $parameters[$name];
        }
        return $result;
    }
}
