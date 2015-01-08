<?php

class ServerHealthControllerTest extends PHPUnit_Framework_TestCase
{

    public function testOk ()
    {
        $serverHealthController = new ServerHealthController(new CakeRequest());
        $response               = $serverHealthController->ok();
        $this->assertEquals('Server Health Success.', $response->body());
    }

}
