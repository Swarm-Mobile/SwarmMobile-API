<?php

class ValidatorComponent
{

    public static function isPositiveInt ($value)
    {
        return is_numeric($value) && (int)$value == $value && $value >= 0;
    }

    public static function isPositiveNumber ($value)
    {
        return is_numeric($value) && $value >= 0;
    }

    public static function isSku ($value)
    {
        return preg_match('/^[a-z0-9]{10,20}$/i', $value);
    }

    public static function isBoolean ($value)
    {   
        return (in_array($value, [false, true, 0, 1, '0','1'], true));
    }

    public static function isDate ($value, $format='Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $value);
        return $d && $d->format($format) == $value;
    }
}
