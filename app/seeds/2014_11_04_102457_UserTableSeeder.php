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

    protected $users = [
        ['username' => 'admin', 'password' => 'kitten'],
        ['username' => 'alien', 'password' => 'fromOuterSpace']
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->hasher = new SymfonySecurityHasher();

        foreach ($this->users as  $u) {
            $salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
            $user = new User();
            $user->username = $u['username'];
            $user->salt = $salt;
            $user->password = $this->hasher->make($u['password'], array('salt' => $salt));
            $user->enabled = true;
            $user->save();
        }
    }

}
