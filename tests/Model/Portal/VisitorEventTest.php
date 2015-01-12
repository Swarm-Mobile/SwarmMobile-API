<?php

class VisitorEventTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $visitorEvent = new VisitorEvent();
        $this->assertInstanceOf('VisitorEvent', $visitorEvent);
    }

}
