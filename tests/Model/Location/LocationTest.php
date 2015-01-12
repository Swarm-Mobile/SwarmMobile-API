<?php

class LocationTest extends PHPUnit_Framework_TestCase
{

    protected function setUp ()
    {
        //FixtureManager::prepareTable('', '');
    }

    protected function tearDown ()
    {
        //FixtureManager::prepareTable('', '');
    }

    public function testExceptions ()
    {
        $location = new Location();
        try {
            $location->getDevices();
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testNameAddressCombination ()
    {
        $location        = new Location();
        $locationSetting = new LocationSetting();
        $locationSetting->setLocationId(689);
        $data            = [
            'address1' => $locationSetting->getSettingValue(LocationSetting::ADDRESS1),
            'city'     => $locationSetting->getSettingValue(LocationSetting::CITY),
            'name'     => 'Cinnamon Girl - Kahala Mall'
        ];

        try {
            $location->nameAddressCombination($data, false, 123);
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }

        $count = $location->nameAddressCombination($data, false, 689);
        $this->assertEquals($count, 0);

        $count = $location->nameAddressCombination($data, true, 367);
        $this->assertEquals($count, 1);
    }

    public function testCountryCodeExists ()
    {
        $location = new Location();
        $this->assertTrue($location->countryCodeExists('CA'));
        $this->assertFalse($location->countryCodeExists('AA'));
    }

    public function testGetDevices ()
    {
        $location = new Location();
        $location->read(null, 367);
        $devices  = $location->getDevices();
        $this->assertEquals('0000000081420070', $devices[0]['serial_number']);
    }

}
