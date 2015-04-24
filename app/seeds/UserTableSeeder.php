<?php

use Dime\Core\Model\User;

class UserTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        User::create([
            'id'       => 1,
            'username' => 'admin',
            'salt' => $salt,
            'password' => Hash::make('kitten', array('salt' => $salt))
        ]);

        $salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        User::create([
            'id'       => 2,
            'username' => 'alien',
            'salt' => $salt,
            'password' => Hash::make('fromOuterSpace', array('salt' => $salt))
        ]);
    }
}
