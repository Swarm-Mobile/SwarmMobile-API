<?php

App::uses('APIComponent', 'Controller/Component');

class StoreComponent extends APIComponent {

    public function openHours($params) {
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
            array('store', 'returning'),
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
            $factor = $data['data']['traffic_factor'];
            $factor = 1 + ((empty($factor) ? 0 : $factor / 100));
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $table = 'sessions';
            $oModel = new Model(false, $table, 'swarmdata');
            $oDb = $oModel->getDataSource();

            $sSQL = <<<SQL
SELECT 
    ROUND(COUNT(walkbys)*$factor) as value, 
    hour, 
    date
FROM(
    SELECT 
        DISTINCT sessions.mac_id as walkbys,        
        DATE_FORMAT(convert_tz(time_login,'GMT', :timezone), '%Y-%m-%d') AS date,
        DATE_FORMAT(convert_tz(time_login,'GMT', :timezone), '%k') AS hour
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
            return $this->format($aRes, $data, $params, $start_date, $end_date, '/store/' . __FUNCTION__, 0, 't2');
        }
    }

    public function returning($params) {
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
    COUNT(unique_mac) as value, 
    hour, 
    date
FROM (
    SELECT 
        DISTINCT sessions.mac_id as unique_mac,
        (convert_tz(time_login,'GMT',:timezone)) as max_login,
        DATE_FORMAT(convert_tz(time_login,'GMT', :timezone), '%Y-%m-%d') AS date,
        DATE_FORMAT(convert_tz(time_login,'GMT', :timezone), '%k') AS hour
    FROM sessions
    INNER JOIN mac_address 
      ON sessions.mac_id = mac_address.id
    WHERE (status !='noise' AND NOISE is false) 
      AND (sessionid='instore' OR sessionid='passive' OR sessionid='active' OR sessionid='login') 
      AND time_logout IS NOT NULL
      AND (network_id = :ap_id)
      AND time_login BETWEEN :start_date AND :end_date
    GROUP BY sessions.mac_id
) t2
INNER JOIN network_mac_logins nml
    ON nml.first_logout   < t2.max_login 
    AND nml.mac_id=t2.unique_mac
WHERE nml.network_id= :ap_id
  AND nml.first_logout IS NOT NULL
  AND t2.max_login  IS NOT NULL
GROUP BY date ASC, hour ASC               
SQL;

            $bind = array();
            $bind['timezone'] = $timezone;
            $bind['ap_id'] = $ap_id;
            $bind['start_date'] = $start_date;
            $bind['end_date'] = $end_date;
            $aRes = $oDb->fetchAll($sSQL, $bind);
            return $this->format($aRes, $data, $params, $start_date, $end_date, '/store/' . __FUNCTION__, 0, 't2');
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
            return $this->format($aRes, $data, $params, $start_date, $end_date, '/store/' . __FUNCTION__, 0, 't2');
        }
    }

    public function purchaseInfo($params) {
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

            $register_filter = $data['data']['register_filter'];
            $register_filter = (!empty($register_filter)) ? " AND i.register_id = $register_filter " : '';
            $outlet_filter = $data['data']['outlet_filter'];
            $outlet_filter = (!empty($outlet_filter)) ? " AND i.outlet_id = $outlet_filter " : '';

            $lightspeed_id = (empty($data['data']['lightspeed_id'])) ? 0 : $data['data']['lightspeed_id'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $table = 'invoices';
            $oModel = new Model(false, $table, 'pos');
            $oDb = $oModel->getDataSource();

            $sSQL = <<<SQL
SELECT 
    COUNT(i.invoice_id) as transactions,
    SUM(i.total) as revenue,
    SUM(il.quantity) as total_items,
    DATE_FORMAT(CONVERT_TZ(i.ts,'GMT','$timezone'),'%Y-%m-%d' ) AS date,
    DATE_FORMAT(CONVERT_TZ(i.ts,'GMT','$timezone'), '%k') AS hour
FROM invoices i
INNER JOIN invoice_lines il ON i.invoice_id = il.invoice_id
WHERE i.store_id= $lightspeed_id
    AND i.completed 
    AND i.total != 0 
    $register_filter
    $outlet_filter
    AND i.ts BETWEEN '$start_date' AND '$end_date'
GROUP BY date ASC, hour ASC                
SQL;
            $aRes = $oDb->fetchAll($sSQL);
            return $aRes;
        }
    }

    public function transactions($params) {
        $rules = array(
            'member_id' => array('required', 'int'),
            'start_date' => array('required', 'date'),
            'end_date' => array('required', 'date')
        );
        $this->validate($params, $rules);
        $data = $this->api->internalCall('member', 'data', array('member_id' => $params['member_id']));
        $aRes = $this->api->internalCall('store', 'purchaseInfo', $params);
        list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
        return $this->format($aRes, $data, $params, $start_date, $end_date, '/store/' . __FUNCTION__, 0, 0, __FUNCTION__);
    }

    public function revenue($params) {
        $rules = array(
            'member_id' => array('required', 'int'),
            'start_date' => array('required', 'date'),
            'end_date' => array('required', 'date')
        );
        $this->validate($params, $rules);
        $data = $this->api->internalCall('member', 'data', array('member_id' => $params['member_id']));
        $aRes = $this->api->internalCall('store', 'purchaseInfo', $params);
        list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
        return $this->format($aRes, $data, $params, $start_date, $end_date, '/store/' . __FUNCTION__, 0, 0, __FUNCTION__);
    }

    public function avgTicket($params) {
        $rules = array(
            'member_id' => array('required', 'int'),
            'start_date' => array('required', 'date'),
            'end_date' => array('required', 'date')
        );
        $this->validate($params, $rules);
        $data = $this->api->internalCall('member', 'data', array('member_id' => $params['member_id']));
        $aRes = $this->api->internalCall('store', 'purchaseInfo', $params);
        list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
        $aRes1 = $this->format($aRes, $data, $params, $start_date, $end_date, '/store/' . __FUNCTION__, 0, 0, 'revenue');
        $aRes2 = $this->format($aRes, $data, $params, $start_date, $end_date, '/store/' . __FUNCTION__, 0, 0, 'transactions');
        return $this->calculate($aRes1, $aRes2);   
    }

    public function itemsPerTransaction($params) {
        $rules = array(
            'member_id' => array('required', 'int'),
            'start_date' => array('required', 'date'),
            'end_date' => array('required', 'date')
        );
        $this->validate($params, $rules);
        $data = $this->api->internalCall('member', 'data', array('member_id' => $params['member_id']));
        $aRes = $this->api->internalCall('store', 'purchaseInfo', $params);
        list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
        $aRes1 = $this->format($aRes, $data, $params, $start_date, $end_date, '/store/' . __FUNCTION__, 0, 0, 'total_items');
        $aRes2 = $this->format($aRes, $data, $params, $start_date, $end_date, '/store/' . __FUNCTION__, 0, 0, 'transactions');
        return $this->calculate($aRes1, $aRes2);        
    }

    public function totalDwell($params) {
        $rules = array(
            'member_id' => array('required', 'int'),
            'start_date' => array('required', 'date'),
            'end_date' => array('required', 'date')
        );
        $this->validate($params, $rules);
        $data = $this->api->internalCall('member', 'data', array('member_id' => $params['member_id']));
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('store', __FUNCTION__, $params);
        } else {
            $ap_id = $data['data']['ap_id'];
            $timezone = $data['data']['timezone'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $table = 'sessions';
            $oModel = new Model(false, $table, 'swarmdata');
            $oDb = $oModel->getDataSource();
            $sSQL = <<<SQL
SELECT 
    SUM(dwell_time) as value,
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
            return $this->format($aRes, $data, $params, $start_date, $end_date, '/store/' . __FUNCTION__, 0, 0);
        }
    }

    public function dwell($params) {
        $this->validate($params, $rules);
        $data = $this->api->internalCall('member', 'data', array('member_id' => $params['member_id']));
        $aRes1 = $this->api->internalCall('store', 'totalDwell', $params);
        $aRes2 = $this->api->internalCall('store', 'footTraffic', $params);        
        list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
        return $this->calculate($aRes1, $aRes2, true);   
    }

    public function windowConversion($params) {
        $ft = $this->api->internalCall('store', 'footTraffic', $params);
        $wb = $this->api->internalCall('store', 'walkbys', $params);
        $result = $this->percentify($ft, $wb);
        $result['options'] = array(
            'endpoint' => '/store/' . __FUNCTION__,
            'member_id' => $params['member_id'],
            'start_date' => $params['start_date'],
            'end_date' => $params['end_date'],
        );
        return $result;
    }

    public function conversionRate($params) {
        $tr = $this->api->internalCall('store', 'transactions', $params);
        $ft = $this->api->internalCall('store', 'footTraffic', $params);
        $result = $this->percentify($tr, $ft);
        $result['options'] = array(
            'endpoint' => '/store/' . __FUNCTION__,
            'member_id' => $params['member_id'],
            'start_date' => $params['start_date'],
            'end_date' => $params['end_date'],
        );
        return $result;
    }

}
