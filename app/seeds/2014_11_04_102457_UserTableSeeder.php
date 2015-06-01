<?php

use Dime\Server\Model\User;
use Dime\Server\Hash\SymfonySecurityHasher;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{

    /**
     * @var SymfonySecurityHasher
     */
    protected $hasher;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->hasher = new SymfonySecurityHasher();
        
        $salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        User::create([
            'id' => 1,
            'username' => 'admin',
            'salt' => $salt,
            'password' => $this->hasher->make('kitten', array('salt' => $salt))
        ]);

        $salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        User::create([
            'id' => 2,
            'username' => 'alien',
            'salt' => $salt,
            'password' => $this->hasher->make('fromOuterSpace', array('salt' => $salt))
        ]);
    }

}
