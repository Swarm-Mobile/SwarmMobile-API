<?php

class JsonResponseTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $jsonResponse = new JsonResponse();
        $this->assertInstanceOf('JsonResponse', $jsonResponse);
    }

}
