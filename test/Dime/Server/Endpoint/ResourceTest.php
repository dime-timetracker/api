<?php

namespace Dime\Server\Endpoint;

use Dime\Server\Endpoint\Resource;
use Dime\Server\Entity\ServiceRepository;
use Slim\Http\Response;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\UploadedFile;
use Slim\Http\Uri;
use Doctrine\ORM\EntityManager;

class ResourceTest extends \PHPUnit_Framework_TestCase
{
//    public function testListAction()
//    {
//        $repository = $this
//            ->getMockBuilder(ServiceRepository::class)
//            ->disableOriginalConstructor()
//            ->getMock();
//        $repository->expects($this->once())
//            ->method('findAll')
//            ->will($this->returnValue([]));
//        $entityManager = $this
//            ->getMockBuilder(EntityManager::class)
//            ->disableOriginalConstructor()
//            ->getMock();
//        $entityManager->expects($this->once())
//            ->method('getRepository')
//            ->will($this->returnValue($repository));
//
//        $config = require ROOT_DIR . '/app/config.php';
//
//        $request = $this->requestFactory();
//        $response = new Response;
//
//        $resource = new Resource($config['settings']['api'], $entityManager);
//        $resource->listAction($request, $response, ['resource' => 'service']);
//    }
//
//    protected function requestFactory()
//    {
//        $env = Environment::mock();
//
//        $uri = Uri::createFromString('http://localhost/api/service');
//        $headers = Headers::createFromEnvironment($env);
//        $cookies = [];
//        $serverParams = $env->all();
//        $body = new RequestBody();
//        $uploadedFiles = UploadedFile::createFromEnvironment($env);
//        $request = new Request('GET', $uri, $headers, $cookies, $serverParams, $body, $uploadedFiles);
//        $request = $request = $request->withAttribute('serialize', 'service');
//
//        return $request;
//    }
}