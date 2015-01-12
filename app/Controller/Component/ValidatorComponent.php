<?php

class ValidatorComponent
{

    public static function isPositiveInt ($value)
    {
        return is_numeric($value) && (int) $value == $value && $value >= 0;
    }

    public static function isPositiveNumber ($value)
    {
        return is_numeric($value) && $value >= 0;
    }

    public static function isSku ($value)
    {
        return (bool)preg_match('/^[a-zA-Z0-9]{10,20}$/i', $value);
    }

    public static function isBoolean ($value)
    {
        return (in_array($value, [false, true, 0, 1, '0', '1'], true));
    }

    public static function isDate ($value, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $value);
        return $d && $d->format($format) == $value;
    }

    public static function isTimezone ($value)
    {
        try {
            new DateTimeZone($value);
        }
        catch (Exception $e) {
            return false;
        }
        return true;
    }

    public static function isDeviceType ($value)
    {
        return in_array(strtolower($value), ['portal', 'presence', 'ping']);
    }

}
