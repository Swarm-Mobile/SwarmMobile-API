<?php

class TimeComponent
{

    public static $TZ_CORRECTIONS = [
        'Austrailia NSW'    => 'Australia/NSW',
        'Australia NSW'     => 'Australia/NSW',
        'Australia/Syndey'  => 'Australia/Sydney',
        'Europe/Amsterdam ' => 'Europe/Amsterdam',
        ''                  => 'America/Los_Angeles'
    ];

    public static function getTimezone ($timezone)
    {        
        $timezones = DateTimeZone::listIdentifiers();
        if (!in_array($timezone, $timezones)) {
            $tz = isset(self::$TZ_CORRECTIONS[$timezone]) ?
                    new DateTimeZone(self::$TZ_CORRECTIONS[$timezone]) :
                    new DateTimeZone('America/Los_Angeles');
        }
        else {
            $tz = new DateTimeZone($timezone);
        }
        return $tz;
    }

    public static function convertTimeToGMT ($time, $timezone, $format = 'Y-m-d H:i:s')
    {
        if (!$timezone instanceof DateTimeZone) {
            $timezone = self::getTimezone($timezone);
        }
        $time = new DateTime($time, $timezone);
        $time = $time->setTimezone(new DateTimeZone('GMT'));
        return $time->format($format);
    }

}
