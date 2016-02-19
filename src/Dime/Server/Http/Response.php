<?php

namespace Dime\Server\Http;

use Slim\Http\Response as SlimResponse;

class Response extends SlimResponse
{
    protected $data;

    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = $data;
    }

}
