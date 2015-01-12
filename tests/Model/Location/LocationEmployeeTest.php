<?php

class LocationEmployeeTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $locationEmployee = new LocationEmployee();
        $this->assertInstanceOf('LocationEmployee', $locationEmployee);
    }

}
