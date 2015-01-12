<?php

class SettingGroupTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $settingGroup = new SettingGroup();
        $this->assertInstanceOf('SettingGroup', $settingGroup);
    }

}
