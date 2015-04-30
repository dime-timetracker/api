<?php

namespace Dime\Server\Resource;

use Dime\Server\Resource\Factory;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * FactoryTest
 *
 * @author Danilo Kuehn <dk@nogo-software.de>
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $factory;

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


        $this->factory = new Factory(array(
            'activity' => array(
                'model' => 'Dime\\Server\\Model\\Activity'

            )
        ));
    }
    
    public function testCreate()
    {
        $model = $this->factory->create('activity', [
            'id' => 1,
            'description' => 'Test',
            'rate' => 20
        ]);

        $this->assertNotNull($model);
        $this->assertEquals('Test', $model->description);
        $this->assertEquals('20', $model->rate);
        $this->assertEquals('', $model->id);
    }

    public function testCreateWith()
    {
        $model = $this->factory->create('activity', [
            'id' => 1,
            'description' => 'Test',
            'rate' => 20,
            'customer' => array(
                'name' => 'Customer'
            )
        ], 1);

        $this->assertNotNull($model);
        $this->assertEquals('Test', $model->description);
        $this->assertEquals('20', $model->rate);
        $this->assertEquals('', $model->id);
        $this->assertNotNull($model->customer());
        $this->assertEquals(1, $model->customer()->user_id);
    }

    public function testGetClass()
    {
        $this->assertEquals(
                'Dime\\Server\\Model\\Activity',
                $this->factory->getClass('activity')
        );
    }

    public function testWith()
    {
        $model = $this->factory->with('activity');
        $this->assertNotNull($model);
    }
}
