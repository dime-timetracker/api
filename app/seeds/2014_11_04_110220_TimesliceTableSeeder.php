<?php

use Dime\Server\Model\User;
use Dime\Server\Model\Timeslice;
use Illuminate\Database\Seeder;

class TimesliceTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userId = User::where('username', 'admin')->first()->id;
        Timeslice::unguard();
        Timeslice::create([
            'id' => 1,
            'activity_id' => 1,
            'duration' => 7200,
            'started_at' => '2014-11-05 10:50:54',
            'stopped_at' => '2014-11-05 12:20:54',
            'user_id' => $userId,
        ]);
        Timeslice::create([
            'activity_id' => 2,
            'duration' => 5400,
            'started_at' => '2014-11-05 13:19:34',
            'stopped_at' => '2014-11-05 14:49:34',
            'user_id' => $userId,
        ]);
        Timeslice::create([
            'id' => 3,
            'activity_id' => 3,
            'duration' => 123,
            'started_at' => '2014-11-05 12:20:20',
            'stopped_at' => '2014-11-05 12:22:43',
            'user_id' => $userId,
        ]);
        Timeslice::create([
            'id' => 4,
            'activity_id' => 4,
            'duration' => 4980,
            'started_at' => '2014-11-05 08:24:09',
            'stopped_at' => '2014-11-05 09:47:09',
            'user_id' => $userId,
        ]);
        Timeslice::reguard();
    }

}
