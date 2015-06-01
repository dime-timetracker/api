<?php

use Dime\Server\Model\Service;
use Dime\Server\Model\User;
use Illuminate\Database\Seeder;

class ServiceTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userId = User::where('username', 'admin')->first()->id;
        Service::unguard();
        Service::create([
            'id' => 1,
            'name' => 'Consulting',
            'alias' => 'cons',
            'rate' => 100,
            'user_id' => $userId,
        ]);
        Service::create([
            'id' => 2,
            'name' => 'Requirements',
            'alias' => 'req',
            'rate' => 100,
            'user_id' => $userId,
        ]);
        Service::create([
            'id' => 3,
            'name' => 'Development',
            'alias' => 'dev',
            'rate' => 70,
            'user_id' => $userId,
        ]);
        Service::create([
            'id' => 4,
            'name' => 'Testing',
            'alias' => 'test',
            'rate' => 40,
            'user_id' => $userId,
        ]);
        Service::create([
            'id' => 5,
            'name' => 'Documentation',
            'alias' => 'doc',
            'rate' => 70,
            'user_id' => $userId,
        ]);
        Service::create([
            'id' => 6,
            'name' => 'Project Management',
            'alias' => 'pm',
            'rate' => 80,
            'user_id' => $userId,
        ]);
        Service::create([
            'id' => 7,
            'name' => 'Quality Assurance',
            'alias' => 'qa',
            'rate' => 70,
            'user_id' => $userId,
        ]);
        Service::create([
            'id' => 8,
            'name' => 'System Analysis',
            'alias' => 'sa',
            'rate' => 100,
            'user_id' => $userId,
        ]);
        Service::create([
            'id' => 9,
            'name' => 'Support',
            'alias' => 'sup',
            'rate' => 80,
            'user_id' => $userId,
        ]);
        Service::create([
            'id' => 10,
            'name' => 'Infrastructure',
            'alias' => 'inf',
            'rate' => 70,
            'user_id' => $userId,
        ]);
        Service::reguard();
    }

}
