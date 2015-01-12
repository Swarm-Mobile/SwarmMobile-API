<?php

class AuthenticationListenerTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $authenticationListener = new AuthenticationListener();
        $this->assertInstanceOf('AuthenticationListener', $authenticationListener);
    }

}
