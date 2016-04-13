<?php

namespace Dime\Server\Functional;

use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Uri;

use function Dime\Server\Functional\acceptHeader;

class AcceptHeaderTest extends \PHPUnit_Framework_TestCase
{

    public function testAcceptHeader()
    {
        $uri = Uri::createFromString('https://example.com:443/foo/bar?abc=123');
        $headers = new Headers([
            'accept' => 'application/json',
        ]);
        $body = new RequestBody();
        $request = new Request('GET', $uri, $headers, [], [], $body);


        $result = acceptHeader($request);
        $this->assertEquals('application/json', $result);
    }

}