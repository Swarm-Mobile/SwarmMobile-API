<?php

class TimeComponentTest extends PHPUnit_Framework_TestCase
{

    public function testConvertTimeToGMT ()
    {
        $this->assertEquals(
            TimeComponent::convertTimeToGMT('2014-01-01 09:00:00', 'America/Los_Angeles'), 
            '2014-01-01 17:00:00'
        );
        $this->assertEquals(
            TimeComponent::convertTimeToGMT('2014-01-01 09:00:00', 'America/Los_Angs'), 
            '2014-01-01 17:00:00'
        );
        $this->assertEquals(
            TimeComponent::convertTimeToGMT('2014-01-01 09:00:00', 'America/Los_Angeles', 'Y-m-d'), 
            '2014-01-01'
        );
        try {
            TimeComponent::convertTimeToGMT('2014-01-01 09:00:aa', 'America/Los_Angeles');
            $this->assertTrue(false);
        } catch(Exception $e){
            $this->assertTrue(true);
        }
    }

    public function testGetTimezone ()
    {
        $corrections = [
            'Austrailia NSW'    => 'Australia/NSW',
            'Australia NSW'     => 'Australia/NSW',
            'Australia/Syndey'  => 'Australia/Sydney',
            'Europe/Amsterdam ' => 'Europe/Amsterdam',
            ''                  => 'America/Los_Angeles'
        ];
        foreach($corrections as $error=>$correction){
            $this->assertEquals(TimeComponent::getTimezone($error), new DateTimeZone($correction));            
        }
        $this->assertEquals(TimeComponent::getTimezone('America/Los_Angeles'), new DateTimeZone('America/Los_Angeles'));
        $this->assertEquals(TimeComponent::getTimezone('America/Los_Ang'), new DateTimeZone('America/Los_Angeles'));
        $this->assertEquals(TimeComponent::getTimezone('Europe/Dublin'), new DateTimeZone('Europe/Dublin'));        
        
    }

}
