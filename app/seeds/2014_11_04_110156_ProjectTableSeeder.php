<?php

use Dime\Server\Model\Customer;
use Dime\Server\Model\Project;
use Dime\Server\Model\User;

class ProjectTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userId = User::where('username', 'admin')->first()->id;
        $customers = [
            'cwe' => Customer::where('alias', 'cwe')->first()->id,
            'ac'  => Customer::where('alias', 'ac')->first()->id,
        ];
        Project::create([
            'id'          => 1,
            'name'        => 'PHPUGL Coding Weekend',
            'alias'       => 'cwe',
            'customer_id' => $customers['cwe'],
            'user_id'     => $userId,
        ]);
        Project::create([
            'id'          => 2,
            'name'        => 'PHP Usergroup Meetup July',
            'alias'       => 'phpugl',
            'customer_id' => $customers['ac'],
            'user_id'     => $userId,
        ]);
    }
}
