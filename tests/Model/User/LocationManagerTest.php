<?php

class LocationManagerTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $locationManager = new LocationManager();
        $this->assertInstanceOf('LocationManager', $locationManager);
    }

}
