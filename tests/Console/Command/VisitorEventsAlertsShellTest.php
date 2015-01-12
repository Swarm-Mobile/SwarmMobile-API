<?php

class VisitorEventsAlertsShellTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $visitorEventsAlertShell = new VisitorEventsAlertsShell();
        $this->assertInstanceOf('VisitorEventsAlertsShell', $visitorEventsAlertShell);
    }

}
