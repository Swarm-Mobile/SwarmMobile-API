<?php

class SettingTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $setting = new Setting();
        $this->assertInstanceOf('Setting', $setting);
    }

}
