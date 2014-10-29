<?php

require_once('SettingComponent.php');

function scape ($string)
{
    $search  = array ("\\", "\x00", "\n", "\r", "'", '"', "\x1a");
    $replace = array ("\\\\", "\\0", "\\n", "\\r", "\'", '\"', "\\Z");
    return str_replace($search, $replace, $string);
}

function get_date ($interval)
{
    $date = new DateTime($interval);
    return $date->format('Y-m-d');
}

function formatValue ($value, $dataType, $oLocation)
{
    switch ($dataType) {
        case 'percentage':
            return round($value, 2) . '%';
        case 'time':
            return makeHMS($value);
        case 'currency':
            $currency = coalesce(settVal('currency', $oLocation['Setting']), '$');
            return $currency . $value;
        case 'num':
        default:
            if (is_float($value)) {
                return round($value, 2);
            }
            else {
                return $value;
            }
    }
}

function previousDayRange ($date, $offsetDays)
{
    $ts        = strtotime($date);
    $start     = strtotime("- $offsetDays days", $ts);
    $startDate = $endDate   = date('Y-m-d', $start);
    return array ($startDate, $endDate);
}

function previousWeekRange ($date)
{
    $ts        = strtotime($date);
    $start     = strtotime(((date('w', $ts) == 1) ? '- 7 days' : 'last monday - 7 days'), $ts);
    $startDate = date('Y-m-d', $start);
    $endDate   = date('Y-m-d', strtotime('next sunday', $start));
    return array ($startDate, $endDate);
}

function previousMonthRange ($date)
{
    $ts        = strtotime($date);
    $start     = strtotime('first day of last month', $ts);
    $startDate = date('Y-m-d', $start);
    $endDate   = date('Y-m-d', strtotime('last day of last month', $ts));
    return array ($startDate, $endDate);
}

function fetchXML ($mapi, $xmlUrl)
{
    $xml = null;
    try {
        $result = $mapi->getXML($xmlUrl);
        $xml    = new SimpleXMLElement($result);
    }
    catch (Exception $e) {
        writetolog("\nException while fetching XML with URL [$xmlUrl]\n" . $e->getMessage() . "\n");
        writetolog($e->getTraceAsString());
    }
    return $xml;
}

function setEnvironment ()
{
    $htaccess = file_get_contents(__DIR__ . '/../../../.htaccess');
    $pattern  = '/.*SetEnv server_location "(.*)"/';
    if (preg_match_all($pattern, $htaccess, $matches)) {
        putenv('server_location=' . $matches[1][0]);
        $_SERVER['server_location'] = $matches[1][0];
    }
}

function coalesce ($a, $b)
{
    return !empty($a) ? $a : $b;
}

function preDebug ($toPrint, $die = true)
{
    echo '<pre>';
    var_dump($toPrint);
    echo '</pre>';
    if ($die) {
        die();
    }
}

function settId ($setting_name)
{
    return SettingComponent::id($setting_name);
}

function settVal ($setting_name, $setting_array)
{
    return SettingComponent::value($setting_name, $setting_array);
}

function settDefaults ($setting_name, $setting_array)
{
    return SettingComponent::defaults($setting_name, $setting_array);
}

function formatDate ($date = false, $hour = false)
{
    if (!$hour) {
        $return = new DateTime($date);
        return date_format($return, 'D M d');
    }
    elseif (!$date) {
        $return = new DateTime('2014-01-01 ' . ' ' . $hour . ':00:00');
        return date_format($return, 'hA');
    }
    $return = new DateTime($date . ' ' . $hour . ':00:00');
    return date_format($return, 'hA D M d');
}

function validEmail ($email)
{
    return is_valid_email_address($email);
}

function ensurePath ($destination)
{
    $folders = explode('/', $destination);
    $path    = realpath(__DIR__ . DS . '..' . DS . '..' . DS . 'webroot');
    foreach ($folders as $folder) {
        $path .= $folder . DS;
        if (!is_dir($path)) {
            mkdir($path);
        }
    }
}

/**
 * Nightclub timezones
 */
function getNightclubTZ ()
{
    return array (
        'eastcoast_time'      => 'eastcoast_time',
        'pacific_time'        => 'pacific_time',
        'central_time'        => 'central_time',
        'mountain_time'       => 'mountain_time',
        'eastaustralian_time' => 'eastaustralian_time'
    );
}

/**
 * POS providers
 */
function getPosProviders ()
{
    return array (
        'no_pos'        => 'no_pos',
        'lsCloud'       => 'lsCloud',
        'mos'           => 'mos',
        'lsPro'         => 'lsPro',
        'vend'          => 'vend',
        'erply'         => 'erply',
        'g9'            => 'g9',
        'ascend'        => 'ascend',
        'edge'          => 'edge',
        'microsoft_rms' => 'g9',
        'quickbooks'    => 'quickbooks',
        'counterpoint'  => 'counterpoint',
        'rpro8'         => 'rpro8',
        'rpro9'         => 'rpro9',
        'camcommerce'   => 'camcommerce',
    );
}

/* Fist dates */

function firstPurchase ($location_id, $timezone = 'America/Los_Angeles')
{
    $data      = $this->api->internalCall('location', 'data', array ('location_id' => $params['location_id']));
    $timezone  = $data['data']['timezone'];
    $oLocation = new Location();
    $oLocation = $oLocation->find('first', ['conditions' => ['Location.id' => $location_id]]);
    $sSQL      = <<<SQL
SELECT DATE(convert_tz(ts,'GMT', '$timezone')) as first_date
FROM invoices s
WHERE store_id = :store_id
  AND ts IS NOT NULL
  AND ts > '2013-01-01 00:00:00'
ORDER BY ts ASC
LIMIT 1
SQL;
    $oModel    = new Model(false, 'stores', 'pos');
    $oDb       = $oModel->getDataSource();
    $aRes      = $oDb->fetchAll($sSQL, [':store_id' => settVal('pos_store_id', $oLocation['Setting'])]);
    if (!empty($aRes)) {
        return $aRes[0][0]['first_date'];
    }
    return null;
}

function firstSession ($location_id, $timezone = 'America/Los_Angeles')
{
    $data      = $this->api->internalCall('location', 'data', array ('location_id' => $params['location_id']));
    $timezone  = $data['data']['timezone'];
    $oLocation = new Location();
    $oLocation = $oLocation->find('first', ['conditions' => ['Location.id' => $location_id]]);
    $sSQL      = <<<SQL
SELECT DATE(convert_tz(time_login,'GMT', '$timezone')) as first_date
FROM sessions s
WHERE network_id = :network_id
  AND time_login IS NOT NULL
  AND time_login > '2013-01-01 00:00:00'
ORDER BY ts ASC
LIMIT 1
SQL;
    $oDb       = DBComponent::getInstance('sessions', 'swarmdata');
    $aRes      = $oDb->fetchAll($sSQL, [':network_id' => settVal('network_id', $oLocation['Setting'])]);
    if (!empty($aRes)) {
        return $aRes[0][0]['first_date'];
    }
    return null;
}

function firstSensor ($location_id, $timezone = 'America/Los_Angeles')
{
    $sSQL = <<<SQL
SELECT DATE(convert_tz(ts,'GMT', '$timezone')) as first_date
FROM visitorEvent s
WHERE location_id = :location_id
  AND ts IS NOT NULL
  AND ts > '2014-07-24 00:00:00'
ORDER BY ts ASC
LIMIT 1
SQL;
    $oDb  = DBComponent::getInstance('visitorEvent', 'portal');
    $aRes = $oDb->fetchAll($sSQL, [':location_id' => $location_id]);
    if (!empty($aRes)) {
        return $aRes[0][0]['first_date'];
    }
    return null;
}

/* */

function getDeviceTypesInLocation ($location_id)
{
    $to_return = [];
    $oDb       = DBComponent::getInstance('visitorEvent', 'portal');
    $aRes      = $oDb->fetchAll(
            "SELECT count(*) c FROM visitorEvent WHERE location_id = :location_id", [':location_id' => $location_id]
    );
    if (!empty($aRes) && $aRes[0][0]['c'] > 0) {
        $to_return[] = 'portal';
    }

    $oDb  = DBComponent::getInstance('location_setting', 'backstage');
    $aRes = $oDb->fetchAll(
            "SELECT value FROM location_setting ls WHERE location_id = :location_id AND setting_id = 6", [':location_id' => $location_id]
    );
    if (!empty($aRes) && is_numeric($aRes[0]['ls']['value']) && !empty($aRes[0]['ls']['value'])) {
        $to_return[] = 'presence';
    }
    return $to_return;
}
