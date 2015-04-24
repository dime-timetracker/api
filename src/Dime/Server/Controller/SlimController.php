<?php
namespace Dime\Server\Controller;

use Slim\Slim;

interface SlimController
{
    public function enable(Slim $app);
}
