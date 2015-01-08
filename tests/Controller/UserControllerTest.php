<?php

class UserControllerTest extends PHPUnit_Framework_TestCase
{

    protected function setUp ()
    {
        //FixtureManager::prepareTable('', '');
    }

    protected function tearDown ()
    {
        FixtureManager::prepareTable('swarm_backstage', 'user');
        FixtureManager::prepareTable('swarm_backstage', 'employee');
        FixtureManager::prepareTable('swarm_backstage', 'developer');
        FixtureManager::prepareTable('swarm_backstage', 'reseller');
        FixtureManager::prepareTable('swarm_backstage', 'accountmanager');
        FixtureManager::prepareTable('swarm_backstage', 'locationmanager');
        FixtureManager::prepareTable('swarm_backstage', 'locationmanager_location');
        FixtureManager::prepareTable('swarm_backstage', 'location_employee');
        FixtureManager::prepareTable('swarm_backstage', 'user_location_report');
    }

    public function testLocationModel ()
    {
        $userController = new UserController();
        $this->assertEquals(new Location(), $userController->getLocation());
        $userController->setLocation(new Location(1));
        $this->assertEquals(new Location(1), $userController->getLocation());
    }

    public function testEmployeeModel ()
    {
        $userController = new UserController();
        $this->assertEquals(new Employee(), $userController->getEmployee());
        $userController->setEmployee(new Employee(1));
        $this->assertEquals(new Employee(1), $userController->getEmployee());
    }

    public function testLocationManagerModel ()
    {
        $userController = new UserController();
        $this->assertEquals(new LocationManager(), $userController->getLocationManager());
        $userController->setLocationManager(new LocationManager(1));
        $this->assertEquals(new LocationManager(1), $userController->getLocationManager());
    }

    public function testLocationEmployeeModel ()
    {
        $userController = new UserController();
        $this->assertEquals(new LocationEmployee(), $userController->getLocationEmployee());
        $userController->setLocationEmployee(new LocationEmployee(1));
        $this->assertEquals(new LocationEmployee(1), $userController->getLocationEmployee());
    }

    public function testLocationLocationManagerModel ()
    {
        $userController = new UserController();
        $this->assertEquals(new LocationLocationmanager(), $userController->getLocationLocationmanager());
        $userController->setLocationLocationmanager(new LocationLocationmanager(1));
        $this->assertEquals(new LocationLocationmanager(1), $userController->getLocationLocationmanager());
    }

    public function testUserModel ()
    {
        $userController = new UserController();
        $this->assertEquals(new User(), $userController->getUser());
        $userController->setUser(new User(1));
        $this->assertEquals(new User(1), $userController->getUser());
    }

    public function testUserLocationReportModel ()
    {
        $userController = new UserController();
        $this->assertEquals(new UserLocationReport(), $userController->getUserLocationReport());
        $userController->setUserLocationReport(new UserLocationReport(1));
        $this->assertEquals(new UserLocationReport(1), $userController->getUserLocationReport());
    }

    public function testRegisterExceptions ()
    {
        $request                 = new CakeRequest();
        $userController          = new UserController();
        $userController->request = &$request;
        try {
            $request->data = [];
            $userController->register();
            $this->assertTrue(false);
        }
        catch (Swarm\RequestValidationException $e) {
            $this->assertEquals(SwarmErrorCodes::VALIDATION_ERROR, $e->getCode());
        }
    }

    public function testRegister ()
    {
        $request                 = new CakeRequest();
        $userController          = new UserController();
        $userController->request = &$request;
        $request->data           = [
            'username'        => 'testa',
            'email'           => 'test@test.com',
            'password'        => '123456789',
            'confirmPassword' => '123456789',
            'firstname'       => 'Test',
            'lastname'        => 'Surname'
        ];
        $response                = $userController->register();
        $result                  = json_decode($response->body(), true);
        $this->assertTrue($response->statusCode() == 201);
        $this->assertEmpty(array_diff(['uuid', 'user_id', 'locationmanager_id'], array_keys($result['data'])));
    }

    public function testLoginExceptions ()
    {
        $request                 = new CakeRequest();
        $userController          = new UserController();
        $userController->request = &$request;
        try {
            $request->data = [];
            $userController->login();
            $this->assertTrue(false);
        }
        catch (Swarm\RequestValidationException $e) {
            $this->assertEquals(SwarmErrorCodes::VALIDATION_ERROR, $e->getCode());
        }

        try {
            $request->data = [
                'username' => 'testa',
                'password' => 'whatever',
            ];
            $userController->login();
            $this->assertTrue(false);
        }
        catch (Swarm\UnprocessableEntityException $e) {
            $this->assertEquals(SwarmErrorCodes::INVALID_CREDENTIALS, $e->getCode());
        }
    }

    public function testLogin ()
    {
        $request                 = new CakeRequest();
        $userController          = new UserController();
        $userController->request = &$request;
        $request->data           = [
            'username' => 'admin',
            'password' => 'asdfg',
        ];
        $response                = $userController->login();
        $this->assertTrue($response->statusCode() == 200);
    }

    public function testGetSettingsExceptions ()
    {
        $request                 = new CakeRequest();
        $userController          = new UserController();
        $userController->request = &$request;
        try {
            $request->query = [];
            $userController->getSettings();
            $this->assertTrue(false);
        }
        catch (Swarm\RequestValidationException $e) {
            $this->assertEquals(SwarmErrorCodes::VALIDATION_ERROR, $e->getCode());
        }

        try {
            $request->query = ['uuid' => 123];
            $userController->getSettings();
            $this->assertTrue(false);
        }
        catch (Swarm\UnprocessableEntityException $e) {
            $this->assertEquals(SwarmErrorCodes::USER_GETSETTINGS_USER_NOTFOUND, $e->getCode());
        }
    }

    public function testGetSettings ()
    {
        //Account Manager
        $data           = ['uuid' => '53b5bcbc87b9f'];
        $request        = new CakeRequest();
        $request->query = $data;
        $userController = new UserController($request);
        $response       = $userController->getSettings();
        $result         = json_decode($response->body(), true);
        //var_dump($result);
        $this->assertTrue($response->statusCode() == 200);

        //Location Manager
        $data           = ['uuid' => '53b5bcbcd1efc'];
        $request        = new CakeRequest();
        $request->query = $data;
        $userController = new UserController($request);
        $response       = $userController->getSettings();
        $this->assertTrue($response->statusCode() == 200);

        //Employee
        $data           = ['uuid' => '53e54336ceba9'];
        $request        = new CakeRequest();
        $request->query = $data;
        $userController = new UserController($request);
        $response       = $userController->getSettings();
        $this->assertTrue($response->statusCode() == 200);
    }

    public function testUpdateSettingsExceptions ()
    {
        $request                 = new CakeRequest();
        $userController          = new UserController();
        $userController->request = &$request;
        try {
            $request->data = [];
            $userController->updateSettings();
            $this->assertTrue(false);
        }
        catch (Swarm\RequestValidationException $e) {
            $this->assertEquals(SwarmErrorCodes::VALIDATION_ERROR, $e->getCode());
        }

        try {
            $request->data = ['uuid' => 123];
            $userController->updateSettings();
            $this->assertTrue(false);
        }
        catch (Swarm\UnprocessableEntityException $e) {
            $this->assertEquals(SwarmErrorCodes::USER_UPDATESETTINGS_USER_NOTFOUND, $e->getCode());
        }
    }

    public function testUpdateSettings ()
    {
        $request                 = new CakeRequest();
        $userController          = new UserController();
        $userController->request = &$request;
        $probes                  = [
            'accountmanager'  => ['uuid' => '53b5bcbc87b9f', 'username' => 'holaa'],
            'locationmanager' => ['uuid' => '53b5bcbcd1efc', 'LocationManager' => ['firstname' => 'tortuga']],
            'employee'        => ['uuid' => '53e54336ceba9', 'Employee' => ['firstname' => 'tortuga']]
        ];
        foreach ($probes as $role => $data) {
            $request->data = $data;
            $response      = $userController->updateSettings();
            $this->assertTrue($response->statusCode() == 202);
        }
    }

    public function testUpdatePasswordExceptions ()
    {
        $request                 = new CakeRequest();
        $userController          = new UserController();
        $userController->request = &$request;
        try {
            $request->data = [];
            $userController->updatePassword();
            $this->assertTrue(false);
        }
        catch (Swarm\RequestValidationException $e) {
            $this->assertEquals(SwarmErrorCodes::VALIDATION_ERROR, $e->getCode());
        }

        try {
            $request->data = [
                'uuid'            => 123,
                'currentPassword' => '123456',
                'password'        => '123456',
                'confirmPassword' => '123456'
            ];
            $userController->updatePassword();
            $this->assertTrue(false);
        }
        catch (Swarm\UnprocessableEntityException $e) {
            $this->assertEquals(SwarmErrorCodes::USER_UPDATEPASSWORD_USER_NOTFOUND, $e->getCode());
        }

        try {
            $request->data = [
                'uuid'            => '53b5bcbcd1efc',
                'currentPassword' => '1122121312',
                'password'        => '123454',
                'confirmPassword' => '123454'
            ];
            $userController->updatePassword();
            $this->assertTrue(false);
        }
        catch (Swarm\UnprocessableEntityException $e) {
            $this->assertEquals(SwarmErrorCodes::USER_UPDATEPASSWORD_PASSWORD_MISMATCH, $e->getCode());
        }
    }

    public function testUpdatePassword ()
    {
        $request        = new CakeRequest();
        $request->data  = [
            'uuid'            => '53b5bcbcd1efc',
            'currentPassword' => 'asdfg',
            'password'        => '123454',
            'confirmPassword' => '123454'
        ];
        $userController = new UserController($request);
        $response       = $userController->updatePassword();
        $this->assertEquals($response->statusCode(), 200);
    }

    public function testLocationsExceptions ()
    {
        $request                 = new CakeRequest();
        $userController          = new UserController();
        $userController->request = &$request;
        try {
            $request->query = [];
            $userController->locations();
            $this->assertTrue(false);
        }
        catch (Swarm\RequestValidationException $e) {
            $this->assertEquals(SwarmErrorCodes::VALIDATION_ERROR, $e->getCode());
        }

        try {
            $request->query = ['uuid' => 123];
            $userController->locations();
            $this->assertTrue(false);
        }
        catch (Swarm\UnprocessableEntityException $e) {
            $this->assertEquals(SwarmErrorCodes::USER_LOCATIONS_USER_NOTFOUND, $e->getCode());
        }
    }

    public function testLocations ()
    {
        $request                 = new CakeRequest();
        $userController          = new UserController();
        $userController->request = &$request;
        $request->query          = ['uuid' => '53e54336ceba9'];
        $response                = $userController->locations();
        $this->assertTrue($response->statusCode() == 200);
    }

}
