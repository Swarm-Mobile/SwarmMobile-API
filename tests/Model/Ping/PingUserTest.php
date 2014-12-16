<?php

class PingUserTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $pingUser = new PingUser();
        $this->assertInstanceOf('PingUser', $pingUser);
    }

}
