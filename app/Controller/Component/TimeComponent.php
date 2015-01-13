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

    public static function previousDayRange($date, $offsetDays)
    {
        $ts = strtotime($date);
        $start = strtotime("- $offsetDays days", $ts);
        $startDate = $endDate = date('Y-m-d', $start);
        return array($startDate, $endDate);
    }

    public static function previousWeekRange($date)
    {
        $ts = strtotime($date);
        $start = strtotime(((date('w', $ts) == 1) ? '- 7 days' : 'last monday - 7 days'), $ts);
        $startDate = date('Y-m-d', $start);
        $endDate = date('Y-m-d', strtotime('next sunday', $start));
        return array($startDate, $endDate);
    }

    public static function previousMonthRange($date)
    {
        $ts = strtotime($date);
        $start = strtotime('first day of last month', $ts);
        $startDate = date('Y-m-d', $start);
        $endDate = date('Y-m-d', strtotime('last day of last month', $ts));
        return array($startDate, $endDate);
    }
    
    public static function getDayString($date)
    {
        $ts  = strtotime($date);
        $day = date('l', $ts);
        return $day;
    }
}
