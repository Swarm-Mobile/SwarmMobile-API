<?php

class DeviceControllerTest extends PHPUnit_Framework_TestCase
{

    protected function setUp ()
    {
        //FixtureManager::prepareTable('', '');
    }

    protected function tearDown ()
    {        
        FixtureManager::prepareTable('swarm_backstage', 'device');
    }
    
    public function testLocationModel ()
    {
        $deviceController = new DeviceController();
        $this->assertEquals(new Location(), $deviceController->getLocation());
        $deviceController->setLocation(new Location(1));
        $this->assertEquals(new Location(1), $deviceController->getLocation());
    }

    public function testUserModel ()
    {
        $deviceController = new DeviceController();
        $this->assertEquals(new User(), $deviceController->getUser());
        $deviceController->setUser(new User(1));
        $this->assertEquals(new User(1), $deviceController->getUser());
    }

    public function testDeviceModel ()
    {
        $deviceController = new DeviceController();
        $this->assertEquals(new Device(), $deviceController->getDevice());
        $deviceController->setDevice(new Device(1));
        $this->assertEquals(new Device(1), $deviceController->getDevice());
    }

    public function testAssignExceptions ()
    {
        $request                   = new CakeRequest();
        $deviceController          = new DeviceController();
        $deviceController->request = &$request;
        $request->query            = [];
        try {
            $deviceController->assign();
            $this->assertTrue(false);
        }
        catch (Swarm\RequestValidationException $e) {
            $this->assertEquals(SwarmErrorCodes::VALIDATION_ERROR, $e->getCode());
        }
    }

    public function testAssign ()
    {
        $probes = [
            [
                'data'   => [
                    'location_id'   => 689,
                    'user_id'       => 123,
                    'type'          => 'portal',
                    'serial_number' => 123,
                    'ts'            => date('Y-m-d H:i:s')],
                'result' => true
            ],
            [
                'data'   => [
                    'location_id'   => 689,
                    'user_id'       => 101,
                    'type'          => 'portal',
                    'serial_number' => '0000000081420070',
                    'ts'            => date('Y-m-d H:i:s')
                ],
                'result' => true
            ]
        ];

        $request                   = new CakeRequest();
        $deviceController          = new DeviceController();
        $deviceController->request = &$request;
        foreach ($probes as $probe) {
            $request->data = $probe['data'];
            $response      = $deviceController->assign();
            $result        = json_decode($response->body(), true);
            $expected      = ['device_assigned', 'message'];
            $this->assertEmpty(array_diff($expected, array_keys($result)));
            $this->assertEquals($probe['result'], $result['device_assigned']);
        }
    }

    public function testCheckForUpdatesExceptions ()
    {
        $request                   = new CakeRequest();
        $deviceController          = new DeviceController();
        $deviceController->request = &$request;
        $request->query            = [];
        try {
            $deviceController->checkForUpdates();
            $this->assertTrue(false);
        }
        catch (Swarm\RequestValidationException $e) {
            $this->assertEquals(SwarmErrorCodes::VALIDATION_ERROR, $e->getCode());
        }

        $request->query = ['type' => 'portal', 'serial_number' => '0000000081420070', 'user_id' => 101, 'firmware_version' => '0.023'];
        try {
            $deviceController->checkForUpdates();
            $this->assertTrue(false);
        }
        catch (Swarm\UnprocessableEntityException $e) {
            $this->assertEquals(SwarmErrorCodes::DEVICE_CHECKFORUPDATES_PORTAL_FIRMWARE_INVALID, $e->getCode());
        }
    }

    public function testCheckForUpdates ()
    {
        $request                   = new CakeRequest();
        $deviceController          = new DeviceController();
        $deviceController->request = &$request;
        $probes                    = [
            ['data' => ['type' => 'portal', 'serial_number' => '0000000081420070', 'user_id' => 101, 'firmware_version' => '1.11']],
            ['data' => ['type' => 'presence', 'serial_number' => '123456789', 'user_id' => 101, 'firmware_version' => '1.11']],
            ['data' => ['type' => 'ping', 'serial_number' => '71420777', 'user_id' => 101, 'firmware_version' => '1.11']]
        ];
        foreach ($probes as $probe) {
            $request->query = $probe['data'];
            $response       = $deviceController->checkForUpdates();
            $result         = json_decode($response->body(), true);
            $this->assertEmpty(array_diff(array_keys($result), [
                'update_available',
                'update_firmware',
                'firmware_version',
                'source'
            ]));
        }
    }

    public function testStatusExceptions ()
    {
        $request                   = new CakeRequest();
        $deviceController          = new DeviceController();
        $deviceController->request = &$request;
        $request->query            = [];
        try {
            $deviceController->getStatus();
            $this->assertTrue(false);
        }
        catch (Swarm\RequestValidationException $e) {
            $this->assertEquals(SwarmErrorCodes::VALIDATION_ERROR, $e->getCode());
        }
        try {
            $deviceController->setStatus();
            $this->assertTrue(false);
        }
        catch (Swarm\RequestValidationException $e) {
            $this->assertEquals(SwarmErrorCodes::VALIDATION_ERROR, $e->getCode());
        }
    }

    public function testStatus ()
    {
        $request          = new CakeRequest();
        $request->data    = [
            'location_id' => 367 , 
            'user_id' => 101, 
            'type' => 'portal', 
            'serial_number' => '0000000081420070', 
            'battery_level' => 100
        ];
        $deviceController = new DeviceController($request);
        $response = $deviceController->setStatus();
        $result = json_decode($response->body(),true);
        $this->assertEquals(100, $result['battery_level']);
    }

}
