<?php

class PingSessionTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $pingSession = new PingSession();
        $this->assertInstanceOf('PingSession', $pingSession);
    }

}
