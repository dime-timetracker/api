<?php

namespace Dime\Server\Model;

use Dime\Server\Model\Activity;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * FactoryTest
 *
 * @author Danilo Kuehn <dk@nogo-software.de>
 */
class ActivityTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $database = new Capsule();
        $database->addConnection(array(
            'driver' =>    'mysql',
            'host' =>      '127.0.0.1',
            'database' =>  'dime_laravel',
            'username' =>  'root',
            'password' =>  '',
            'charset' =>   'utf8',
            'collation' => 'utf8_general_ci'
        ));
        $database->setAsGlobal();
        $database->bootEloquent();
    }
    
    public function testFiltered()
    {
        $list = Activity::with(['customer', 'project', 'service'])->filtered('@cwe')->get();

        $this->assertCount(4, $list);


        $list = Activity::with(['customer', 'project', 'service'])->filtered(':inf')->get();

        $this->assertCount(2, $list);
    }

}
