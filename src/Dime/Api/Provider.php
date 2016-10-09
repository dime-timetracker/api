<?php

namespace Dime\Api;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

use Dime\Api\Action\DeleteAction;
use Dime\Api\Action\GetAction;
use Dime\Api\Action\ListAction;
use Dime\Api\Action\LoginAction;
use Dime\Api\Action\LogoutAction;
use Dime\Api\Action\PostAction;
use Dime\Api\Action\PutAction;
use Dime\Api\Action\RegisterAction;

class Provider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        DeleteAction::class,
        GetAction::class,
        ListAction::class,
        LoginAction::class,
        LogoutAction::class,
        PostAction::class,
        PutAction::class,
        RegisterAction::class,
    ];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->getContainer()->add(DeleteAction::class)
          ->withArgument($this->getContainer());

        $this->getContainer()->add(GetAction::class)
          ->withArgument($this->getContainer());

        $this->getContainer()->add(ListAction::class)
          ->withArgument($this->getContainer());

        $this->getContainer()->add(LoginAction::class)
          ->withArgument($this->getContainer());

        $this->getContainer()->add(LogoutAction::class)
          ->withArgument($this->getContainer());

        $this->getContainer()->add(PostAction::class)
          ->withArgument($this->getContainer());

        $this->getContainer()->add(PutAction::class)
          ->withArgument($this->getContainer());

        $this->getContainer()->add(RegisterAction::class)
          ->withArgument($this->getContainer());
    }
}
