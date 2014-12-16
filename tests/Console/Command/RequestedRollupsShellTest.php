<?php

class RequestedRollupsShellTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $requestedRollupsShell = new RequestedRollupsShell();
        $this->assertInstanceOf('RequestedRollupsShell', $requestedRollupsShell);
    }

}
