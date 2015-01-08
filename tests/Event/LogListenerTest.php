<?php

class LogListenerTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $logListener = new LogListener();
        $this->assertInstanceOf('LogListener', $logListener);
    }

}
