<?php

class LocationSettingTest extends PHPUnit_Framework_TestCase
{

    protected function setUp ()
    {
        //FixtureManager::prepareTable('', '');
    }

    protected function tearDown ()
    {
        //FixtureManager::prepareTable('', '');
    }

    public function testCreate ()
    {
        $locationSetting = new LocationSetting();
        $locationSetting->create(['LocationSetting' => ['location_id' => 689]]);
        $this->assertEquals($locationSetting->getLocationId(), 689);
    }

    public function testLocationId ()
    {
        $locationSetting = new LocationSetting();
        $this->assertEquals($locationSetting->getLocationId(), null);
        $locationSetting->setLocationId(689);
        $this->assertEquals($locationSetting->getLocationId(), 689);
    }

    public function testGetSettingValue ()
    {
        $locationSetting = new LocationSetting();
        $locationSetting->setLocationId(1494);
        $currency        = $locationSetting->getSettingValue(LocationSetting::CURRENCY);
        $this->assertEquals('$', $currency);
    }

    public function testGetTimezone ()
    {
        $locationSetting = new LocationSetting();
        $locationSetting->setLocationId(689);
        $timezone        = $locationSetting->getTimezone();
        $this->assertEquals('Pacific/Guam', $timezone);

        $locationSetting->setLocationId(123);
        $timezone = $locationSetting->getTimezone();
        $this->assertEquals('America/Los_Angeles', $timezone);
    }

    public function testGetOpenHours ()
    {
        $locationSetting = new LocationSetting();
        $locationSetting->setLocationId(689);
        $openHours       = $locationSetting->getOpenHours();
        $expected        = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];        
        $this->assertEmpty(array_diff($expected, array_keys($openHours)));
        foreach ($expected as $day) {
            $expected = ['isOpen', 'open', 'close'];            
            $this->assertEmpty(array_diff($expected, array_keys($openHours[$day])));
        }
    }

    public function testGetFirstPurchaseDate ()
    {
        $locationSetting = new LocationSetting();
        $locationSetting->setLocationId(689);
        $firstPurchase   = $locationSetting->getFirstPurchaseDate();
        $this->assertEquals('2014-01-01', $firstPurchase);
    }

    public function testGetFirstSessionDate ()
    {
        $locationSetting = new LocationSetting();
        $locationSetting->setLocationId(689);
        $firstSession    = $locationSetting->getFirstSessionDate();
        $this->assertEquals('2014-10-01', $firstSession);
    }

    public function testGetFirstSensorDate ()
    {
        $locationSetting = new LocationSetting();
        $locationSetting->setLocationId(367);
        $firstSensor     = $locationSetting->getFirstSensorDate();
        $this->assertEquals('2014-09-30', $firstSensor);
    }

    public function testGetDeviceTypesAssociated ()
    {
        $locationSetting = new LocationSetting();
        $locationSetting->setLocationId(367);
        $devices         = $locationSetting->getDeviceTypesAssociated();
        $this->assertTrue(in_array('portal', $devices));
        $this->assertTrue(in_array('presence', $devices));
    }

}
