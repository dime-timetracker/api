<?php

namespace Dime\Server\Functional;

use Psr\Http\Message\ServerRequestInterface;

function acceptHeader(ServerRequestInterface $request, array $allowed = [
        'application/json',
        'application/xml',
        'text/xml',
        'text/yml',
        'text/html'
    ])
{
    $acceptHeader = $request->getHeader('accept');
    $result = $allowed[0];
    
    if (!empty($acceptHeader)) {
        $found = false;
        $acceptHeader = splitHeader($acceptHeader[0]);
        $acceptHeader = array_filter($acceptHeader, function () {})


        foreach ($acceptHeader as $accept) {
            foreach ($allowed as $name) {
                if (trim($accept) === $name) {
                    $result = $name;
                    $found = true;
                    break;
                }
            }
            if ($found) {
                break;
            }
        }
    }

    return $result;
}

function splitHeader($header) {
    return array_map('trim', preg_split('/\s*[;,]\s*/', $header));
}