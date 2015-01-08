<?php

class LocationControllerTest extends PHPUnit_Framework_TestCase
{

    protected function tearDown ()
    {
        FixtureManager::prepareTable('swarm_backstage', 'location');
        FixtureManager::prepareTable('swarm_backstage', 'location_setting');
        FixtureManager::prepareTable('swarm_backstage', 'locationmanager_location');
        FixtureManager::prepareTable('swarm_backstage', 'location_employee');
    }

    protected function setUp ()
    {
        //FixtureManager::prepareTable('', '');
    }

    public function testInvoiceModel ()
    {
        $locationController = new LocationController();
        $this->assertEquals(new Invoice(), $locationController->getInvoice());
        $locationController->setInvoice(new Invoice(1));
        $this->assertEquals(new Invoice(1), $locationController->getInvoice());
    }

    public function testLocationSettingModel ()
    {
        $locationController = new LocationController();
        $this->assertEquals(new LocationSetting(), $locationController->getLocationSetting());
        $locationController->setLocationSetting(new LocationSetting(1));
        $this->assertEquals(new LocationSetting(1), $locationController->getLocationSetting());
    }

    public function testLocationModel ()
    {
        $locationController = new LocationController();
        $this->assertEquals(new Location(), $locationController->getLocation());
        $locationController->setLocation(new Location(1));
        $this->assertEquals(new Location(1), $locationController->getLocation());
    }

    public function testSettingModel ()
    {
        $locationController = new LocationController();
        $this->assertEquals(new Setting(), $locationController->getSetting());
        $locationController->setSetting(new Setting(1));
        $this->assertEquals(new Setting(1), $locationController->getSetting());
    }

    public function testEmployeeModel ()
    {
        $locationController = new LocationController();
        $this->assertEquals(new Employee(), $locationController->getEmployee());
        $locationController->setEmployee(new Employee(1));
        $this->assertEquals(new Employee(1), $locationController->getEmployee());
    }

    public function testDeveloperModel ()
    {
        $locationController = new LocationController();
        $this->assertEquals(new Developer(), $locationController->getDeveloper());
        $locationController->setDeveloper(new Developer(1));
        $this->assertEquals(new Developer(1), $locationController->getDeveloper());
    }

    public function testResellerModel ()
    {
        $locationController = new LocationController();
        $this->assertEquals(new Reseller(), $locationController->getReseller());
        $locationController->setReseller(new Reseller(1));
        $this->assertEquals(new Reseller(1), $locationController->getReseller());
    }

    public function testAccountManagerModel ()
    {
        $locationController = new LocationController();
        $this->assertEquals(new AccountManager(), $locationController->getAccountManager());
        $locationController->setAccountManager(new AccountManager(1));
        $this->assertEquals(new AccountManager(1), $locationController->getAccountManager());
    }

    public function testLocationEmployeeModel ()
    {
        $locationController = new LocationController();
        $this->assertEquals(new LocationEmployee(), $locationController->getLocationEmployee());
        $locationController->setLocationEmployee(new LocationEmployee(1));
        $this->assertEquals(new LocationEmployee(1), $locationController->getLocationEmployee());
    }

    public function testLocationManagerModel ()
    {
        $locationController = new LocationController();
        $this->assertEquals(new LocationManager(), $locationController->getLocationManager());
        $locationController->setLocationManager(new LocationManager(1));
        $this->assertEquals(new LocationManager(1), $locationController->getLocationManager());
    }

    public function testLocationLocationManagerModel ()
    {
        $locationController = new LocationController();
        $this->assertEquals(new LocationLocationManager(), $locationController->getLocationLocationManager());
        $locationController->setLocationLocationManager(new LocationLocationManager(1));
        $this->assertEquals(new LocationLocationManager(1), $locationController->getLocationLocationManager());
    }

    public function testUserModel ()
    {
        $locationController = new LocationController();
        $this->assertEquals(new User(), $locationController->getUser());
        $locationController->setUser(new User(1));
        $this->assertEquals(new User(1), $locationController->getUser());
    }

    public function testHighlightsExceptions ()
    {
        $request                     = new CakeRequest();
        $locationController          = new LocationController();
        $locationController->request = &$request;

        try {
            $request->query = [];
            $locationController->highlights();
            $this->assertTrue(false);
        }
        catch (Swarm\RequestValidationException $e) {
            $this->assertEquals(SwarmErrorCodes::VALIDATION_ERROR, $e->getCode());
        }

        try {
            $request->query = ['location_id' => 123];
            $locationController->highlights();
            $this->assertTrue(false);
        }
        catch (Swarm\UnprocessableEntityException $e) {
            $this->assertEquals(SwarmErrorCodes::LOCATION_HIGHLIGHTS_STORE_NOTFOUND, $e->getCode());
        }
    }

    public function testHighlights ()
    {
        $request                     = new CakeRequest();
        $locationController          = new LocationController();
        $locationController->request = &$request;
        $request->query              = ['location_id' => 689, 'start_date' => '2014-01-01', 'end_date' => '2014-01-31'];
        $response                    = $locationController->highlights();

        $result = json_decode($response->body(), true);
        $this->assertTrue($response->statusCode() == 200);
        $this->assertEquals(['Biggest Ticket', 'Best Hour', 'Best Day'], array_keys($result));
    }

    public function testGetSettingsExceptions ()
    {
        $request                     = new CakeRequest();
        $locationController          = new LocationController();
        $locationController->request = &$request;

        try {
            $request->query = [];
            $locationController->getSettings();
            $this->assertTrue(false);
        }
        catch (Swarm\RequestValidationException $e) {
            $this->assertEquals(SwarmErrorCodes::VALIDATION_ERROR, $e->getCode());
        }

        try {
            $request->query = ['location_id' => 123];
            $locationController->getSettings();
            $this->assertTrue(false);
        }
        catch (Swarm\UnprocessableEntityException $e) {
            $this->assertEquals(SwarmErrorCodes::LOCATION_GETSETTINGS_LOCATION_NOTFOUND, $e->getCode());
        }
    }

    public function testGetSettings ()
    {
        $request                     = new CakeRequest();
        $locationController          = new LocationController();
        $locationController->request = &$request;
        $request->query              = ['location_id' => 689];
        $response                    = $locationController->getSettings();
        $result                      = json_decode($response->body(), true);

        $this->assertTrue($response->statusCode() == 200);
        $this->assertEmpty(array_diff(['data', 'options'], array_keys($result)));
        $this->assertEmpty(array_diff(['endpoint', 'location_id'], array_keys($result['options'])));
        $this->assertEmpty(array_diff(['name', 'settings'], array_keys($result['data'])));
        foreach ($result['data']['settings'] as $k => $v) {
            $expected = ['label', 'setting_id', 'value', 'description'];
            $this->assertEmpty(array_diff($expected, array_keys($result['data']['settings'][$k])));
        }
    }

    public function testUpdateSettingsExceptions ()
    {
        $request                     = new CakeRequest();
        $locationController          = new LocationController();
        $locationController->request = &$request;

        try {
            $request->data = [];
            $locationController->updateSettings();
            $this->assertTrue(false);
        }
        catch (Swarm\RequestValidationException $e) {
            $this->assertEquals(SwarmErrorCodes::VALIDATION_ERROR, $e->getCode());
        }

        try {
            $request->data = ['location_id' => 123, 'uuid' => '1231221'];
            $locationController->updateSettings();
            $this->assertTrue(false);
        }
        catch (Swarm\UnprocessableEntityException $e) {
            $this->assertEquals(SwarmErrorCodes::LOCATION_UPDATESETTINGS_LOCATION_NOTFOUND, $e->getCode());
        }
    }

    public function testUpdateSettings ()
    {
        $request                     = new CakeRequest();
        $locationController          = new LocationController();
        $locationController->request = &$request;

        $request->data = [
            'uuid'        => '53b5bcbc87b9f',
            'location_id' => 689,
            'country'     => 'ES',
            'address1'    => 'My Awesome address',
            'city'        => 'San Awesome',
            'Location'    => [
                'name' => 'New Location Name',
                689    => ['pos_store_id' => 121123]
            ]
        ];
        $response      = $locationController->updateSettings();
        $result        = json_decode($response->body(), true);
        $this->assertArrayHasKey('success', $result['message']);
    }

    public function testAvailableSettingsExceptions ()
    {
        $request                     = new CakeRequest();
        $locationController          = new LocationController();
        $locationController->request = &$request;
        try {
            $request->data = [];
            $locationController->availableSettings();
            $this->assertTrue(false);
        }
        catch (Swarm\RequestValidationException $e) {
            $this->assertEquals(SwarmErrorCodes::VALIDATION_ERROR, $e->getCode());
        }
    }

    public function testAvailableSettings ()
    {
        $request                     = new CakeRequest();
        $locationController          = new LocationController();
        $locationController->request = &$request;
        $request->query              = ['uuid' => '53b5bcbc87b9f'];
        $response                    = $locationController->availableSettings();
        $result                      = json_decode($response->body(), true);
        $this->assertTrue($response->statusCode() == 200);
        $this->assertEquals(['data'], array_keys($result));
        $this->assertEquals(['settings'], array_keys($result['data']));
        foreach ($result['data']['settings'] as $k => $v) {
            $expected = ['id', 'label', 'desc', 'default'];
            $this->assertEmpty(array_diff($expected, array_keys($result['data']['settings'][$k])));
        }
    }

    public function testOpenHoursExceptions ()
    {
        $request                     = new CakeRequest();
        $locationController          = new LocationController();
        $locationController->request = &$request;
        try {
            $request->data = [];
            $locationController->openHours();
            $this->assertTrue(false);
        }
        catch (Swarm\RequestValidationException $e) {
            $this->assertEquals(SwarmErrorCodes::VALIDATION_ERROR, $e->getCode());
        }
    }

    public function testOpenHours ()
    {
        $request                     = new CakeRequest();
        $locationController          = new LocationController();
        $locationController->request = &$request;
        $request->query              = ['location_id' => 689];
        $response                    = $locationController->openHours();
        $result                      = json_decode($response->body(), true);

        $this->assertTrue($response->statusCode() == 200);
        $this->assertEquals(['data', 'options'], array_keys($result));

        $expected = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $this->assertEmpty(array_diff($expected, array_keys($result['data'])));

        foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
            $expected = ['open', 'close'];
            $this->assertEmpty(array_diff($expected, array_keys($result['data'][$day])));
        }
    }

    public function testCreateExceptions ()
    {
        $request                     = new CakeRequest();
        $locationController          = new LocationController();
        $locationController->request = &$request;
        try {
            $request->data = [];
            $locationController->create();
            $this->assertTrue(false);
        }
        catch (Swarm\RequestValidationException $e) {
            $this->assertEquals(SwarmErrorCodes::VALIDATION_ERROR, $e->getCode());
        }

        try {
            $request->data = [
                'uuid'     => '123',
                'name'     => 'New Location',
                'Location' => [
                    'city'    => 'San Francisco',
                    'zipcode' => 12345,
                    'country' => 'US'
                ]
            ];
            $locationController->create();
            $this->assertTrue(false);
        }
        catch (Swarm\UnprocessableEntityException $e) {
            $this->assertEquals(SwarmErrorCodes::LOCATION_CREATE_USER_NOTFOUND, $e->getCode());
        }
    }

    public function testCreate ()
    {
        $request                     = new CakeRequest();
        $locationController          = new LocationController();
        $locationController->request = &$request;

        $data = [
            'name'     => 'New Location',
            'Location' => [
                'address1' => '255 Bush Street',
                'city'     => 'San Francisco',
                'zipcode'  => 12345,
                'country'  => 'US'
            ]
        ];

        $probes = [
            'employee'        => ['uuid' => '53e54336ceba9', 'address1' => 'Employee Manager St.'],
            'locationmanager' => ['uuid' => '53b5bcbcd1efc', 'address1' => 'Location Manager St.'],
            'accountmanager'  => ['uuid' => '53b5bcbc87b9f', 'address1' => 'Account Manager St.'],
            'reseller'        => ['uuid' => '53b5bd519a96f', 'address1' => 'Reseller St.'],
            'developer'       => ['uuid' => '53c8642c68e09', 'address1' => 'Developer St.'],
        ];
        foreach ($probes as $role => $params) {
            $data['uuid']                 = $params['uuid'];
            $data['Location']['address1'] = $params['address1'];
            $request->data                = $data;
            $response                     = $locationController->create();
            $result                       = json_decode($response->body(), true);
            $this->assertArrayHasKey('success', $result['message']);
            $this->assertArrayHasKey($role . '_id', $result['data']);
            $this->assertArrayHasKey('location_id', $result['data']);
        }
    }

}
