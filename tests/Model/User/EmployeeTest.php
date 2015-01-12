<?php

class EmployeeTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $employee = new Employee();
        $this->assertInstanceOf('Employee', $employee);
    }

}
