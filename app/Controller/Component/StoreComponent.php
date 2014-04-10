<?php

App::uses('APIComponent', 'Controller/Component');

class StoreComponent extends APIComponent {

    private function format($aRes, $data, $params, $start_date, $end_date, $endpoint, $t1, $t2) {
        $cResult = array();
        foreach ($aRes as $oRow) {
            $weekday = strtolower(date('l', strtotime($oRow[$t2]['date'])));
            $date = $oRow[$t2]['date'];
            $hour = $oRow[$t2]['hour'];
            $cValue = (int) $oRow[$t1]['value'];
            if (
                    (int) $hour >= (int) strstr($data['data'][$weekday . '_open'], ':', true) &&
                    (int) $hour <= (int) strstr($data['data'][$weekday . '_close'], ':', true)
            ) {
                $cResult['data']['breakdown'][$date]['hours'][$hour]['open'] = true;
                @$cResult['data']['breakdown'][$date]['totals']['open'] += $cValue;
                @$cResult['data']['totals']['open'] += $cValue;
            } else {
                $cResult['data']['breakdown'][$date]['hours'][$hour]['open'] = false;
                @$cResult['data']['breakdown'][$date]['totals']['close'] += $cValue;
                @$cResult['data']['totals']['close'] += $cValue;
            }
            @$cResult['data']['breakdown'][$date]['totals']['total'] += $cValue;
            @$cResult['data']['breakdown'][$date]['hours'][$hour]['total'] += $cValue;
            @$cResult['data']['totals']['total'] += $cValue;
        }
        $cResult['options'] = array(
            'endpoint' => $endpoint,
            'member_id' => $params['member_id'],
            'start_date' => $params['start_date'],
            'end_date' => $params['end_date'],
        );
        return $this->fillBlanks($cResult, $start_date, $end_date);
    }

    private function calculate($aRes1, $aRes2) {
        $result = array();
        foreach ($aRes1['data']['breakdown'] as $date => $values) {
            foreach ($values['hours'] as $hour => $v) {
                foreach ($v as $i => $j) {
                    $a = $aRes1['data']['breakdown'][$date]['hours'][$hour][$i];
                    $b = $aRes2['data']['breakdown'][$date]['hours'][$hour][$i];
                    $result['data']['breakdown'][$date]['hours'][$hour][$i] = round($a / $b, 2);
                }
            }
            foreach ($values['totals'] as $k => $v) {
                $a = $aRes1['data']['breakdown'][$date]['totals'][$k];
                $b = $aRes2['data']['breakdown'][$date]['totals'][$k];
                $result['data']['breakdown'][$date]['totals'][$k] = round($a / $b, 2);
            }
        }
        foreach ($aRes1['data']['totals'] as $k => $v) {
            $a = $aRes1['data']['totals'][$k];
            $b = $aRes2['data']['totals'][$k];
            $result['data']['totals'][$k] = round($a / $b, 2);
        }
        return $result;
    }

    public function totals($params) {
        $rules = array(
            'member_id' => array('required', 'int'),
            'start_date' => array('required', 'date'),
            'end_date' => array('required', 'date')
        );
        $this->validate($rules, $params);
        $result = array();
        $calls = array(
            array('store', 'walkbys'),
            array('store', 'footTraffic'),
            array('store', 'transactions'),
            array('store', 'revenue'),
            array('store', 'avgTicket'),
            array('store', 'dwell'),
            array('store', 'windowConversion'),
            array('store', 'conversionRate'),
        );
        foreach ($calls as $call) {
            $tmp = $this->api->internalCall($call[0], $call[1], $params);
            $result[$call[1]] = $tmp['data']['totals']['total'];
        }
        return $result;
    }

    public function walkbys($params) {
        $rules = array(
            'member_id' => array('required', 'int'),
            'start_date' => array('required', 'date'),
            'end_date' => array('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('store', __FUNCTION__, $params);
        } else {
            $data = $this->api->internalCall('member', 'data', array('member_id' => $params['member_id']));
            $ap_id = $data['data']['ap_id'];
            $timezone = $data['data']['timezone'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $table = 'sessions';
            $oModel = new Model(false, $table, 'swarmdata');
            $oDb = $oModel->getDataSource();

            $sSQL = <<<SQL
SELECT 
    COUNT(walkbys) as value, 
    hour, 
    date
FROM(
    SELECT 
        DISTINCT sessions.mac_id as walkbys,        
        DATE_FORMAT(convert_tz(time_login,'GMT', :timezone), '%Y-%m-%d') AS date,
        DATE_FORMAT(convert_tz(time_login,'GMT', :timezone), '%H') AS hour
    FROM sessions
    INNER JOIN mac_address 
        ON sessions.mac_id = mac_address.id
    WHERE ( status !='noise' AND NOISE is false) 
      AND (network_id=:ap_id) 
      AND (sessionid='passerby') 
      AND time_login BETWEEN :start_date AND :end_date
    GROUP BY sessions.mac_id
) as t2 GROUP BY date ASC, hour ASC             
SQL;

            $bind = array();
            $bind['timezone'] = $timezone;
            $bind['ap_id'] = $ap_id;
            $bind['start_date'] = $start_date;
            $bind['end_date'] = $end_date;
            $aRes = $oDb->fetchAll($sSQL, $bind);
            return $this->format($aRes, $data, $params, $start_date, $end_date, '/store/walkbys', 0, 't2');
        }
    }

    public function footTraffic($params) {
        $rules = array(
            'member_id' => array('required', 'int'),
            'start_date' => array('required', 'date'),
            'end_date' => array('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('store', __FUNCTION__, $params);
        } else {
            $data = $this->api->internalCall('member', 'data', array('member_id' => $params['member_id']));
            $ap_id = $data['data']['ap_id'];
            $timezone = $data['data']['timezone'];

            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $store_open_compare = $this->storeOpenCompare($data, $timezone);
            $table = 'sessions';
            $oModel = new Model(false, $table, 'swarmdata');
            $oDb = $oModel->getDataSource();

            $sSQL = <<<SQL
SELECT 
    COUNT(foot_traffic) as value, 
    hour, 
    date
FROM(
    SELECT 
        DISTINCT sessions.mac_id as foot_traffic,
        DATE(CONVERT_TZ(time_login,'GMT', '$timezone' )) AS date,
        DATE_FORMAT(CONVERT_TZ(time_login,'GMT', '$timezone' ), '%k') AS hour
    FROM sessions
    INNER JOIN mac_address 
        ON (sessions.mac_id = mac_address.id)
    WHERE (mac_address.status<>'noise')
      AND (time_logout IS NOT NULL)
      AND $store_open_compare  
      AND sessionid IN ('instore','passive','active','login')
      AND network_id = $ap_id
      AND time_login BETWEEN '$start_date' AND '$end_date'
) as t2 GROUP BY date ASC, hour ASC                     
SQL;
            $aRes = $oDb->fetchAll($sSQL);
            return $this->format($aRes, $data, $params, $start_date, $end_date, '/store/footTraffic', 0, 't2');
        }
    }

    public function transactions($params) {
        $rules = array(
            'member_id' => array('required', 'int'),
            'start_date' => array('required', 'date'),
            'end_date' => array('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('store', __FUNCTION__, $params);
        } else {
            $data = $this->api->internalCall('member', 'data', array('member_id' => $params['member_id']));
            $timezone = $data['data']['timezone'];
            $lightspeed_id = $data['data']['lightspeed_id'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $table = 'invoices';
            $oModel = new Model(false, $table, 'pos');
            $oDb = $oModel->getDataSource();

            $sSQL = <<<SQL
SELECT 
    COUNT(invoice_id) as value,
    DATE_FORMAT(CONVERT_TZ(invoices.ts,'GMT','$timezone'),'%Y-%m-%d' ) AS date,
    DATE_FORMAT(CONVERT_TZ(ts,'GMT','$timezone'), '%k') AS hour
FROM $table
WHERE store_id= $lightspeed_id
    AND completed 
    AND invoices.total != 0 
    AND {$this->registerFilter($data)}
	{$this->outletFilter($data)}
        invoices.ts BETWEEN '$start_date' AND '$end_date'
GROUP BY date ASC, hour ASC                
SQL;
            $aRes = $oDb->fetchAll($sSQL);
            return $this->format($aRes, $data, $params, $start_date, $end_date, '/store/transactions', 0, 0);
        }
    }

    public function revenue($params) {
        $rules = array(
            'member_id' => array('required', 'int'),
            'start_date' => array('required', 'date'),
            'end_date' => array('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('store', __FUNCTION__, $params);
        } else {
            $data = $this->api->internalCall('member', 'data', array('member_id' => $params['member_id']));
            $timezone = $data['data']['timezone'];
            $lightspeed_id = $data['data']['lightspeed_id'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $table = 'invoices';
            $oModel = new Model(false, $table, 'pos');
            $oDb = $oModel->getDataSource();

            $sSQL = <<<SQL
SELECT 
    SUM(total) as revenue,
    DATE_FORMAT(CONVERT_TZ(invoices.ts,'GMT','$timezone'),'%Y-%m-%d' ) AS date,
    DATE_FORMAT(CONVERT_TZ(ts,'GMT','$timezone'), '%k:00') AS hour
FROM $table
WHERE store_id= $lightspeed_id
    AND completed 
    AND invoices.total != 0 
    AND {$this->registerFilter($data)}
	{$this->outletFilter($data)}
        invoices.ts BETWEEN '$start_date' AND '$end_date'
GROUP BY date ASC, hour ASC                   
SQL;
            $aRes = $oDb->fetchAll($sSQL);
            return $this->format($aRes, $data, $params, $start_date, $end_date, '/store/revenue', 0, 0);
        }
    }

    public function avgTicket($params) {
        $rules = array(
            'member_id' => array('required', 'int'),
            'start_date' => array('required', 'date'),
            'end_date' => array('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('store', __FUNCTION__, $params);
        } else {
            $data = $this->api->internalCall('member', 'data', array('member_id' => $params['member_id']));
            $timezone = $data['data']['timezone'];
            $lightspeed_id = $data['data']['lightspeed_id'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $table = 'invoices';
            $oModel = new Model(false, $table, 'pos');
            $oDb = $oModel->getDataSource();

            $sSQL = <<<SQL
SELECT 
    AVG(total) AS avgTicket,
    DATE_FORMAT(CONVERT_TZ(invoices.ts,'GMT','$timezone'),'%Y-%m-%d' ) AS date,
    DATE_FORMAT(CONVERT_TZ(ts,'GMT','$timezone'), '%k:00') AS hour
FROM $table
WHERE store_id= $lightspeed_id
    AND completed 
    AND invoices.total != 0 
    AND {$this->registerFilter($data)}
	{$this->outletFilter($data)}
        invoices.ts BETWEEN '$start_date' AND '$end_date'
GROUP BY date ASC, hour ASC                  
SQL;
            $aRes = $oDb->fetchAll($sSQL);
            return $this->format($aRes, $data, $params, $start_date, $end_date, '/store/footTraffic', 0, 0);
        }
    }

    public function dwell($params) {
        $rules = array(
            'member_id' => array('required', 'int'),
            'start_date' => array('required', 'date'),
            'end_date' => array('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('store', __FUNCTION__, $params);
        } else {
            $data = $this->api->internalCall('member', 'data', array('member_id' => $params['member_id']));
            $ap_id = $data['data']['ap_id'];
            $timezone = $data['data']['timezone'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $table = 'sessions';
            $oModel = new Model(false, $table, 'swarmdata');
            $oDb = $oModel->getDataSource();
            $sSQL = <<<SQL
SELECT 
    AVG(dwell_time) as value,
    DATE_FORMAT(login_min, '%Y-%m-%d') as date,
    DATE_FORMAT(login_min, '%k') AS hour
FROM (
    SELECT 
        ses1.mac_id,
	DATE_FORMAT(convert_tz(time_login,'GMT',:timezone), '%Y-%m-%d') days,
	MIN(CONVERT_TZ(time_login,'GMT',:timezone)) as login_min,
        MAX(CONVERT_TZ(time_logout,'GMT',:timezone)) as logout_max,
	(MAX(UNIX_TIMESTAMP(time_logout))-MIN(UNIX_TIMESTAMP(time_login))) as dwell_time
    FROM `sessions` as ses1
    INNER JOIN mac_address 
        ON ses1.mac_id = mac_address.id
    WHERE status !='noise' 
      AND noise IS false 
      AND time_logout IS NOT NULL 
      AND network_id=:ap_id 
      AND time_login BETWEEN :start_date and :end_date
    GROUP BY ses1.mac_id, days
    HAVING 18000 > dwell_time
    ORDER BY days DESC
) as t2
GROUP BY date ASC, hour ASC       
SQL;
            $bind = array();
            $bind['timezone'] = $timezone;
            $bind['ap_id'] = $ap_id;
            $bind['start_date'] = $start_date;
            $bind['end_date'] = $end_date;
            $aRes = $oDb->fetchAll($sSQL, $bind);
            return $this->format($aRes, $data, $params, $start_date, $end_date, '/store/dwell', 0, 0);
        }
    }

    public function windowConversion($params) {
        $ft = $this->api->internalCall('store', 'footTraffic', $params);
        $wb = $this->api->internalCall('store', 'walkbys', $params);
        return $this->calculate($ft, $wb);
    }

    public function conversionRate($params) {
        $tr = $this->api->internalCall('store', 'transactions', $params);
        $ft = $this->api->internalCall('store', 'footTraffic', $params);
        return $this->calculate($tr, $ft);
    }

    function openHours($params) {
        $data = $this->api->internalCall('member', 'data', $params);
        $return = $this->weekdays;
        foreach ($return as &$v) {
            $v = array(
                $v => array(
                    'open' => $data['data'][$v . '_open'],
                    'close' => $data['data'][$v . '_close']
                )
            );
        }
        $result = array('data' => $return);
        $result['options'] = array(
            'endpoint' => 'store/openHours',
            'member_id' => $params['member_id'],
            'start_date' => $params['start_date'],
            'end_date' => $params['end_date'],
        );
        return $result;
    }

}
