<?php

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../'));
$loader = require_once ROOT_DIR . '/vendor/autoload.php';

\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader([$loader, 'loadClass']);

use Jgut\Slim\Doctrine\EntityManagerBuilder;
use Interop\Container\ContainerInterface;
use Slim\App;
use Dime\Server\Serializer\Construction\DoctrineObjectConstructor;

$app = new App(require ROOT_DIR . '/app/config.php');

$container = $app->getContainer();

// Dependencies

$container['entityManager'] = function (ContainerInterface $container) {
    return EntityManagerBuilder::build($container->settings['doctrine']);
};

$container['serializer'] = function (ContainerInterface $container) {
    $serializer = JMS\Serializer\SerializerBuilder::create();

    $serializer->setDebug($container->settings['displayErrorDetails']);
    $serializer->setObjectConstructor(new DoctrineObjectConstructor($container->entityManager, new JMS\Serializer\Construction\UnserializeObjectConstructor()));

    return $serializer->build();
};

$container['hasher'] = function () {
    return new Dime\Server\Hash\SymfonySecurityHasher();
};

$container['validator'] = function () {
    return Symfony\Component\Validator\Validation::createValidatorBuilder()
        ->enableAnnotationMapping()
        ->getValidator();
};

// Middleware

$container['Dime\Server\Middleware\Authorization'] = function (ContainerInterface $container) {
    $config = $container->settings['auth'];

    $repository = $container->entityManager->getRepository($config['access']);

    $access = [];
    $collection = $repository->findAll();
    foreach ($collection as $entity) {
        $user = $entity->getUser();
        if (!isset($access[$user->getUsername()])) {
            $access[$user->getUsername()] = [];
        }

        $access[$user->getUsername()][] = [
            'id' => $user->getId(),
            'client' => $entity->getClient(),
            'token' => $entity->getToken(),
            'expires' => $entity->getUpdatedAt()
        ];
    }

    return new Dime\Server\Middleware\Authorization($config, $access);
};

$container['Dime\Server\Middleware\ResourceType'] = function (ContainerInterface $container) {
    return new Dime\Server\Middleware\ResourceType($container->settings['api']);
};

$container['Dime\Server\Middleware\ContentNegotiation'] = function (ContainerInterface $container) {
    return new Dime\Server\Middleware\ContentNegotiation($container->settings['api']);
};

$container['Dime\Server\Middleware\ContentTransformer'] = function (ContainerInterface $container) {
    return new Dime\Server\Middleware\ContentTransformer($container->serializer);
};

$container['Dime\Server\Middleware\Validation'] = function (ContainerInterface $container) {
    return new Dime\Server\Middleware\Validation($container->validator);
};

// Endpoints

$container['Dime\Server\Endpoint\Authentication'] = function (ContainerInterface $container) {
    return new Dime\Server\Endpoint\Authentication(
            $container->settings['api'], 
            $container->entityManager, 
            $container->hasher
    );
};

$container['Dime\Server\Endpoint\Resource'] = function (ContainerInterface $container) {
    return new Dime\Server\Endpoint\Resource(
            $container->settings['api'], 
            $container->entityManager,
            $container->validator
    );
};

// Bootstrap routes
$routing = require_once ROOT_DIR . '/app/routing.php';
foreach ($routing as $name => $config) {
    $r = $app->map(
            $config['map'], 
            $config['route'], 
            $config['controller']
    );

    if (isset($config['middleware'])) {
        foreach ($config['middleware'] as $mw) {
            $r = $r->add($mw);
        }
    }
}

$app->run();
