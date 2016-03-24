<?php

namespace Dime\Api\Entity;

class TimesliceTest extends \PHPUnit_Framework_TestCase
{

    public function testCalculateDuration()
    {
        $timeslice = new Timeslice();
        $timeslice->setStartedAt(new \DateTime('2016-02-24 15:30:00'));
        $timeslice->setStoppedAt(new \DateTime('2016-02-24 15:32:00'));
        $this->assertEquals(120, $timeslice->calculateDuration());
    }
}
