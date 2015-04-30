<?php

namespace Dime\Server\View;

/**
 * JsonTest
 *
 * @author Danilo Kuehn <dk@nogo-software.de>
 */
class JsonTest extends \PHPUnit_Framework_TestCase
{
    public function testRender()
    {
        $view = new \Dime\Server\View\Json();

        $output = $view->fetch('', ['test' => 1]);

        $this->assertEquals('{"test":1}', $output);
    }
}
