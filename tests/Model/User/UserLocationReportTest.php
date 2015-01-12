<?php

class UserLocationReportTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $userLocationReport = new UserLocationReport();
        $this->assertInstanceOf('UserLocationReport', $userLocationReport);
    }

}
