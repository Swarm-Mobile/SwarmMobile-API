<?php

require_once('SettingComponent.php');
require_once('rfc822.php');

function scape($string){
    $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
    $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");
    return str_replace($search, $replace, $string);
}
function get_date($interval) {
    $date = new DateTime($interval);
    return $date->format('Y-m-d');
}

function signSymbol($pct) {
    return ($pct > 0) ? '&#9650;' : (($pct < 0) ? '&#9660;' : ' ');
}

function signVerbiage($pct) {
    return ($pct > 0) ? 'increase' : (($pct < 0) ? 'decrease' : 'flat');
}

function signColor($pct) {
    return ($pct > 0) ? 'success' : (($pct < 0) ? 'error' : '');
}

function signColorInline($pct) {
    return ($pct > 0) ? 'color:#468847;' : (($pct < 0) ? 'color:#b94a48;' : '');
}

function formatValue($value, $dataType, $oLocation) {
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
            } else {
                return $value;
            }
    }
}

function previousDayRange($date, $offsetDays) {
    $ts = strtotime($date);
    $start = strtotime("- $offsetDays days", $ts);
    $startDate = $endDate = date('Y-m-d', $start);
    return array($startDate, $endDate);
}

function previousWeekRange($date) {
    $ts = strtotime($date);
    $start = strtotime(((date('w', $ts) == 1) ? '- 7 days' : 'last monday - 7 days'), $ts);
    $startDate = date('Y-m-d', $start);
    $endDate = date('Y-m-d', strtotime('next sunday', $start));
    return array($startDate, $endDate);
}

function previousMonthRange($date) {
    $ts = strtotime($date);
    $start = strtotime('first day of last month', $ts);
    $startDate = date('Y-m-d', $start);
    $endDate = date('Y-m-d', strtotime('last day of last month', $ts));
    return array($startDate, $endDate);
}

function fetchXML($mapi, $xmlUrl) {
    $xml = null;
    try {
        $result = $mapi->getXML($xmlUrl);
        $xml = new SimpleXMLElement($result);
    } catch (Exception $e) {
        writetolog("\nException while fetching XML with URL [$xmlUrl]\n" . $e->getMessage() . "\n");
        writetolog($e->getTraceAsString());
    }
    return $xml;
}

function writetolog($message, $logfile = null) {
    if (!empty($logfile) && file_exists($logfile)) {
        $handle = fopen($logfile, "a");
        $line = "$message\n";
        fwrite($handle, $line);
        fclose($handle);
    }
}

function setEnvironment() {
    $htaccess = file_get_contents(__DIR__ . '/../../../.htaccess');
    $pattern = '/.*SetEnv server_location "(.*)"/';
    if (preg_match_all($pattern, $htaccess, $matches)) {
        putenv('server_location=' . $matches[1][0]);
        $_SERVER['server_location'] = $matches[1][0];
    }
}

function coalesce($a, $b) {
    return !empty($a) ? $a : $b;
}

function preDebug($toPrint, $die = true) {
    echo '<pre>';
    var_dump($toPrint);
    echo '</pre>';
    if ($die) {
        die();
    }
}

function settId($setting_name) {
    return SettingComponent::id($setting_name);
}

function settVal($setting_name, $setting_array) {
    return SettingComponent::value($setting_name, $setting_array);
}

function settDefaults($setting_name, $setting_array) {
    return SettingComponent::defaults($setting_name, $setting_array);
}

function logoURL($location_id) {
    $extensions = array('jpg', 'jpeg', 'png', 'gif');
    $file = WEBROOT_DIR . '/images/location_photos/photo_' . $location_id;
    $src = Router::url('/images/new-logo.png', true);
    foreach ($extensions as $ext) {
        if (file_exists($file . '.' . $ext)) {
            $src = Router::url('/images/location_photos/photo_' . $location_id . '.' . $ext, true);
            break;
        }
    }
    return $src;
}

function array2csv(array &$array, $first = true) {
    if (count($array) == 0) {
        return null;
    }
    ob_start();
    $df = fopen("php://output", 'w');
    if ($first) {
        fputcsv($df, array_keys(reset($array)));
    }
    foreach ($array as $row) {
        fputcsv($df, $row);
    }
    fclose($df);
    return ob_get_clean();
}

function setCsvHeaders($filename) {
// disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

// force download  
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

// disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}

function setJSONHeaders() {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET");
    header("Access-Control-Allow-Headers: X-PINGOTHER");
    header("Access-Control-Max-Age: 1728000");
    header("Content-Type: application/json; charset=UTF-8");
}

function makeHMS($seconds) {
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

function formatDate($date = false, $hour = false) {
    if (!$hour) {
        $return = new DateTime($date);
        return date_format($return, 'D M d');
    } elseif (!$date) {
        $return = new DateTime('2014-01-01 ' . ' ' . $hour . ':00:00');
        return date_format($return, 'hA');
    }
    $return = new DateTime($date . ' ' . $hour . ':00:00');
    return date_format($return, 'hA D M d');
}

function csvTextToArray($csvText, $hasHeader = true, $header = null) {
    $fh = fopen('data://text/plain,' . $csvText, 'r');
    $rv = array();
    if ($hasHeader) {
        $row = array_map('trim', fgetcsv($fh));
        if (empty($header)) {
            $header = $row;
        }
    }
    while ($row = fgetcsv($fh)) {
        $rv[] = array_combine($header, array_map('trim', $row));
    }
    return $rv;
}

function validEmail($email) {
    return is_valid_email_address($email);
}

function ensurePath($destination) {
    $folders = explode('/', $destination);
    $path = realpath(__DIR__ . DS . '..' . DS . '..' . DS . 'webroot');
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
function getNightclubTZ() {
    return array(
        'eastcoast_time' => 'eastcoast_time',
        'pacific_time' => 'pacific_time',
        'central_time' => 'central_time',
        'mountain_time' => 'mountain_time',
        'eastaustralian_time' => 'eastaustralian_time'
    );
}

/**
 * POS providers
 */
function getPosProviders() {
    return array(
        'no_pos' => 'no_pos',
        'lsCloud' => 'lsCloud',
        'mos' => 'mos',
        'lsPro' => 'lsPro',
        'vend' => 'vend',
        'erply' => 'erply',
        'g9' => 'g9',
        'ascend' => 'ascend',
        'edge' => 'edge',
        'microsoft_rms' => 'g9',
        'quickbooks' => 'quickbooks',
        'counterpoint' => 'counterpoint',
        'rpro8' => 'rpro8',
        'rpro9' => 'rpro9',
        'camcommerce' => 'camcommerce',
    );
}

/*Fist dates*/
function firstPurchase($location_id) {
    $oLocation = new Location();
    $oLocation = $oLocation->find('first', ['conditions' => ['Location.id' => $location_id]]);
    $sSQL = <<<SQL
SELECT DATE(ts) as first_date
FROM invoices s
WHERE store_id = :store_id
  AND ts IS NOT NULL
  AND ts > '2013-01-01 00:00:00'
ORDER BY ts ASC
LIMIT 1
SQL;
    $oModel = new Model(false, 'stores', 'pos');
    $oDb = $oModel->getDataSource();
    $aRes = $oDb->fetchAll($sSQL, [':store_id' => settVal('pos_store_id', $oLocation['Setting'])]);
    if (!empty($aRes)) {
        return $aRes[0][0]['first_date'];
    }
    return null;
}

function firstSession($location_id) {
    $oLocation = new Location();
    $oLocation = $oLocation->find('first', ['conditions' => ['Location.id' => $location_id]]);
    $sSQL = <<<SQL
SELECT DATE(time_login) as first_date
FROM sessions s
WHERE network_id = :network_id
  AND time_login IS NOT NULL
  AND time_login > '2013-01-01 00:00:00'
ORDER BY ts ASC
LIMIT 1
SQL;
    $oDb = DBComponent::getInstance('sessions', 'swarmdata');
    $aRes = $oDb->fetchAll($sSQL, [':network_id' => settVal('network_id', $oLocation['Setting'])]);
    if (!empty($aRes)) {
        return $aRes[0][0]['first_date'];
    }
    return null;
}

function firstSensor($location_id) {
    $sSQL = <<<SQL
SELECT DATE(ts) as first_date
FROM visitorEvent s
WHERE location_id = :location_id
  AND ts IS NOT NULL
  AND ts > '2013-01-01 00:00:00'
ORDER BY ts ASC
LIMIT 1
SQL;
    $oDb = DBComponent::getInstance('visitorEvent', 'portal');
    $aRes = $oDb->fetchAll($sSQL, [':location_id' => $location_id]);
    if (!empty($aRes)) {
        return $aRes[0][0]['first_date'];
    }
    return null;
}