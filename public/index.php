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

$app->post('/login', \Dime\Api\Action\LoginAction::class)->setName('login');
$app->post('/logout', \Dime\Api\Action\LogoutAction::class)->setName('logout')->add('middleware.authorization');
$app->post('/register', \Dime\Api\Action\RegisterAction::class)->setName('register');

$app->group('/api', function () {

    $this->get('/{resource}/{id:\d+}', \Dime\Api\Action\GetAction::class)->setName('resource_get');
    $this->get('/{resource}', \Dime\Api\Action\ListAction::class)->setName('resource_list');
    $this->post('/{resource}', \Dime\Api\Action\PostAction::class)->setName('resource_post');
    $this->put('/{resource}/{id:\d+}', \Dime\Api\Action\PutAction::class)->setName('resource_put');
    $this->delete('/{resource}/{id:\d+}', \Dime\Api\Action\DeleteAction::class)->setName('resource_delete');

})->add('middleware.authorization')
  ->add('middleware.resource');

$app->post('/invoice/html', function (ServerRequestInterface $request, ResponseInterface $response) {
    $invoiceData = json_decode($request->getParam('invoice'), true);
    $renderer = new \Dime\InvoiceRenderer\Renderer();
    $html = $renderer->setTemplate(ROOT_DIR . '/vendor/dime-timetracker/invoice-renderer/templates/default.twig')->html($invoiceData);
    $body = $response->getBody();
    $body->write($html);
})->setName('invoice');

$app->run();
