<?php

use Dime\Core\Model\Customer;
use Dime\Core\Model\User;

class CustomerTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userId = User::where('username', 'admin')->first()->id;

        Customer::create([
            'id'      => 1,
            'name'    => 'CWE Customer',
            'alias'   => 'cwe',
            'user_id' => $userId,
        ]);
        Customer::create([
            'id'      => 2,
            'name'    => 'Another Customer',
            'alias'   => 'ac',
            'user_id' => $userId,
        ]);
        Customer::create([
            'id'      => 1000,
            'name'    => 'Alien Customer',
            'alias'   => 'ac',
            'user_id' => User::where('username', 'alien')->first()->id,
        ]);
    }
}
