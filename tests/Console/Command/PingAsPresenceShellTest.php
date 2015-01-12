<?php

class PingAsPresenceShellTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $pingAsPresence = new PingAsPresenceShell();
        $this->assertInstanceOf('PingAsPresenceShell', $pingAsPresence);
    }

}
