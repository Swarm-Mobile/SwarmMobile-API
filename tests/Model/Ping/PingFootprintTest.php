<?php

class PingFootprintTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $pingFootprint = new PingFootprint();
        $this->assertInstanceOf('PingFootprint', $pingFootprint);
    }

}
