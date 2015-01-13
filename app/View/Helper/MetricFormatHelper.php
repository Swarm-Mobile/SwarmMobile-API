<?php
class MetricFormatHelper extends AppHelper
{
    function formatValue($value, $dataType, $oLocation)
    {
        switch ($dataType) {
            case 'percentage':
                return round($value, 2) . '%';
            case 'time':
                return $this->makeHMS($value);
            case 'currency':
                $currency = $this->coalesce(settVal('currency', $oLocation['Setting']), '$');
                return $currency . $value;
            case 'num':
            default:
                if (is_float($value)) {
                    return round($value, 2);
                } else {
                    return $value;
                }
        }
    }
    
    function signColor($pct)
    {
        return ($pct > 0) ? 'success' : (($pct < 0) ? 'error' : '');
    }
    
    function signColorInline($change)
    {
        return ($change == 'increase') ? 'color:#468847;' : (($change == 'decrease') ? 'color:#b94a48;' : '');
    }
    
    function signVerbiage($change)
    {
        return ($change == 'increase') ? 'increase' : (($change == 'decrease') ? 'decrease' : 'flat');
    }

    function signSymbol($change)
    {
        return ($change == 'increase') ? '&#9650;' : (($change == 'decrease') ? '&#9660;' : ' ');
    }
    
    function coalesce($a, $b)
    {
        return !empty($a) ? $a : $b;
    }

    function makeHMS($seconds)
    {
        if ($seconds) {
            $h = floor($seconds / 3600);
            $m = floor($seconds % 3600 / 60);
            $s = floor($seconds % 3600 % 60);
            $m = ($m > 0) ? ((($m < 10) ? ('0' . $m) : $m) . ":") : (($h > 0) ? '00:' : "");
            $h = ($h > 0) ? ((($h < 10) ? ('0' . $h) : $h) . ":") : "";
            $s = ($s < 10) ? ('0' . $s) : $s;
            return $h . $m . $s;
        }
        return 0;
    }
}