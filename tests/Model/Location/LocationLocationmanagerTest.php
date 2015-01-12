<?php

class LocationLocationmanagerTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $locationLocationManager = new LocationLocationmanager();
        $this->assertInstanceOf('LocationLocationmanager', $locationLocationManager);
    }

}
