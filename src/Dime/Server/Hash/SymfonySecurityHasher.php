<?php

namespace Dime\Server\Hash;

class SymfonySecurityHasher
{

    protected $algorithm = 'sha512';
    protected $iterations = 5000;

    public function __construct($algorithm = 'sha512', $iterations = 5000)
    {
        $this->algorithm = $algorithm;
        $this->iterations = $iterations;
    }

    /**
     * Hash the given value.
     *
     * @param  string  $value
     * @param  array   $options
     * @return string
     *
     * @throws \RuntimeException
     */
    public function make($value, array $options = array())
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

    /**
     * Check the given plain value against a hash.
     *
     * @param  string  $value
     * @param  string  $hashedValue
     * @param  array   $options
     * @return bool
     */
    public function check($value, $hashedValue, array $options = array())
    {
        return $this->make($value, $options) === $hashedValue;
    }

    /**
     * Check if the given hash has been hashed using the given options.
     *
     * @param  string  $hashedValue
     * @param  array   $options
     * @return bool
     */
    public function needsRehash($hashedValue, array $options = array())
    {
        return false;
    }

    /**
     * Merges a password and a salt.
     *
     * @param string $password the password to be used
     * @param string $salt the salt to be used
     *
     * @return string a merged password and salt
     *
     * @throws \InvalidArgumentException
     */
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
