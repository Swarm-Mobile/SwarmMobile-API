<?php

class RequestCacheListenerTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $requestCacheListener = new RequestCacheListener();
        $this->assertInstanceOf('RequestCacheListener', $requestCacheListener);
    }

}
