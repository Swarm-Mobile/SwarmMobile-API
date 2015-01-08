<?php

class ValidatorComponentTest extends PHPUnit_Framework_TestCase
{

    public function testIsBoolean ()
    {
        $this->assertTrue(ValidatorComponent::isBoolean(true));
        $this->assertTrue(ValidatorComponent::isBoolean(1));
        $this->assertTrue(ValidatorComponent::isBoolean('1'));
        
        $this->assertTrue(ValidatorComponent::isBoolean(false));
        $this->assertTrue(ValidatorComponent::isBoolean(0));
        $this->assertTrue(ValidatorComponent::isBoolean('0'));
        
        $this->assertFalse(ValidatorComponent::isBoolean('whatever'));
    }

    public function testIsDate ()
    {
        $this->assertTrue(ValidatorComponent::isDate('2014-01-01'));
        $this->assertTrue(ValidatorComponent::isDate('2014-01-01', 'Y-m-d'));
        $this->assertFalse(ValidatorComponent::isDate('2014-01-01', 'Y'));
        
        $this->assertTrue(ValidatorComponent::isDate('2014-01-01 00:00:00', 'Y-m-d H:i:s'));
        $this->assertFalse(ValidatorComponent::isDate('2014-01-01 00:00:00'));
        
        $this->assertFalse(ValidatorComponent::isDate('whatever'));
        $this->assertFalse(ValidatorComponent::isDate('2014-01-01', 'whatever'));        
        
    }

    public function testIsDeviceType ()
    {        
        $this->assertTrue(ValidatorComponent::isDeviceType('portal'));        
        $this->assertTrue(ValidatorComponent::isDeviceType('PoRTal'));        
        $this->assertTrue(ValidatorComponent::isDeviceType('ping'));        
        $this->assertTrue(ValidatorComponent::isDeviceType('PinG'));        
        $this->assertTrue(ValidatorComponent::isDeviceType('presence'));        
        $this->assertTrue(ValidatorComponent::isDeviceType('presENCE'));        
        $this->assertFalse(ValidatorComponent::isDeviceType('whatever'));        
    }

    public function testIsPositiveInt ()
    {
        $this->assertTrue(ValidatorComponent::isPositiveInt(0));        
        $this->assertTrue(ValidatorComponent::isPositiveInt(1));        
        $this->assertTrue(ValidatorComponent::isPositiveInt(23));        
        $this->assertFalse(ValidatorComponent::isPositiveInt(23.23));        
        $this->assertFalse(ValidatorComponent::isPositiveInt(-1));        
        $this->assertFalse(ValidatorComponent::isPositiveInt('whatever'));        
    }

    public function testIsPositiveNumber ()
    {
        $this->assertTrue(ValidatorComponent::isPositiveNumber(0));        
        $this->assertTrue(ValidatorComponent::isPositiveNumber(1));        
        $this->assertTrue(ValidatorComponent::isPositiveNumber(23));        
        $this->assertTrue(ValidatorComponent::isPositiveNumber(23.23));        
        $this->assertFalse(ValidatorComponent::isPositiveNumber(-1));        
        $this->assertFalse(ValidatorComponent::isPositiveNumber('whatever'));   
    }

    public function testIsSku ()
    {
        $this->assertFalse(ValidatorComponent::isSku('012345678'));        
        $this->assertTrue(ValidatorComponent::isSku('01234567890'));        
        $this->assertTrue(ValidatorComponent::isSku('00112233445566778899'));        
        $this->assertTrue(ValidatorComponent::isSku('0011223344556677889a'));        
        $this->assertTrue(ValidatorComponent::isSku('0011223344556677889B'));        
        $this->assertFalse(ValidatorComponent::isSku('00112233445566778899a'));                
        $this->assertFalse(ValidatorComponent::isSku('whatever'));                        
    }

    public function testIsTimezone ()
    {
        $this->assertTrue(ValidatorComponent::isTimezone('America/Los_Angeles'));       
        $this->assertFalse(ValidatorComponent::isTimezone('whatever'));       
    }

}
