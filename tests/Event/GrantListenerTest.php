<?php

class GrantListenerTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $grantListener = new GrantListener();
        $this->assertInstanceOf('GrantListener', $grantListener);
    }

}
