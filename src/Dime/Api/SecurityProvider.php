<?php

namespace Dime\Api;

class SecurityProvider
{

    private $algorithm = 'sha512';
    private $iterations = 5000;
    private $expires = '1 week';

    public function authenticate(array $user, $password)
    {
        return !empty($user)
            && $this->check(
                $password, 
                $user['password'],
                [ 'salt' => $user['salt'] ]
            );
    }
    
    public function createToken($username, $client)
    {
        return $this->make(uniqid($username . $client . microtime(), true));
    }

    public function expires($from)
    {
        return date('Y-m-d H:i:s', strtotime($this->expires, strtotime($from)));
    }
    
    public function getAlgorithm()
    {
        return $this->algorithm;
    }

    public function setAlgorithm($algorithm)
    {
        $this->algorithm = $algorithm;
        return $this;
    }

    public function getIterations()
    {
        return $this->iterations;
    }

    public function setIterations($iterations)
    {
        $this->iterations = $iterations;
        return $this;
    }

    protected function make($value, array $options = array())
    {
        if (array_key_exists('salt', $options)) {
            $value = $this->mergePasswordAndSalt($value, $options['salt']);
        }
        $digest = hash($this->algorithm, $value, true);
        if ($digest !== false) {
            for ($i = 1; $i < $this->iterations; $i++) {
                $digest = hash($this->algorithm, $digest . $value, true);
            }
        }
        return base64_encode($digest);
    }

    protected function check($value, $hashedValue, array $options = array())
    {
        return $this->make($value, $options) === $hashedValue;
    }
    
    protected function mergePasswordAndSalt($password, $salt)
    {
        if (empty($salt)) {
            return $password;
        }
        if (false !== strrpos($salt, '{') || false !== strrpos($salt, '}')) {
            throw new \InvalidArgumentException('Cannot use { or } in salt.');
        }
        return $password . '{' . $salt . '}';
    }

}
