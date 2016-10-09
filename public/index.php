<?php

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../'));

// Composer autoloading

if (!file_exists(ROOT_DIR . '/vendor/autoload.php')) {
    die('Please do \'composer install\'!');
}
require_once ROOT_DIR . '/vendor/autoload.php';

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;
use Slim\Exception\SlimException;

// Configuration

$configuration = [];
if (file_exists(ROOT_DIR . '/config/parameters.php')) {
    $configuration = require_once ROOT_DIR . '/config/parameters.php';
}
$settings = array_replace_recursive(
    [
        'displayErrorDetails' => true,
        'enableSecurity' => false,
        'allowedResources' => [
            'activities', 'timeslices', 'customers', 'projects', 'services', 'settings', 'tags'
        ]
    ],
    $configuration
);

$app = new \Dime\Server\App($settings);
$app->getContainer()->addServiceProvider(\Dime\Api\Provider::class);

// Authentication routes

$app->post('/login', \Dime\Api\Action\LoginAction::class);
$app->post('/logout', \Dime\Api\Action\LogoutAction::class)->add('middleware.authorization');
$app->post('/register', \Dime\Api\Action\RegisterAction::class);

$app->group('/api', function () {

    $this->get('/{resource}/{id:\d+}', \Dime\Api\Action\GetAction::class);
    $this->get('/{resource}', \Dime\Api\Action\ListAction::class);
    $this->post('/{resource}', \Dime\Api\Action\PostAction::class);
    $this->put('/{resource}/{id:\d+}', \Dime\Api\Action\PutAction::class);
    $this->delete('/{resource}/{id:\d+}', \Dime\Api\Action\DeleteAction::class);

    $this->post('/invoice/html', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        $parsedData = $request->getParsedBody();
        $renderer = new \Dime\InvoiceRenderer\Renderer();
        $html = $renderer->setTemplate(ROOT_DIR . '/vendor/dime-timetracker/invoice-renderer/templates/default.twig')->html($parsedData);
        $body = $response->getBody();
        $body->write($html);
    });

})->add('middleware.authorization')
  ->add('middleware.resource');


$app->run();
