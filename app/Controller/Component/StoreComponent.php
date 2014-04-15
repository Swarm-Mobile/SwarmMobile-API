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

    private function returningByHour($start_date, $end_date, $timezone, $ap_id) {
        $sSQL = <<<SQL
SELECT
    x.hour,
    COUNT(z.login) as value 
FROM(
    SELECT 0 as hour 
    UNION SELECT 1   UNION SELECT 2 	UNION SELECT 3 	
    UNION SELECT 4   UNION SELECT 5	UNION SELECT 6	
    UNION SELECT 7   UNION SELECT 8	UNION SELECT 9	
    UNION SELECT 10  UNION SELECT 11	UNION SELECT 12	
    UNION SELECT 13  UNION SELECT 14	UNION SELECT 15	
    UNION SELECT 16  UNION SELECT 17	UNION SELECT 18	
    UNION SELECT 19  UNION SELECT 20	UNION SELECT 21	
    UNION SELECT 22  UNION SELECT 23
) x
INNER JOIN (
    SELECT login,logout
    FROM (
      SELECT 
        DISTINCT ses1.mac_id as unique_mac,(CONVERT_TZ(time_login,'GMT','$timezone')) as max_login,
        DATE_FORMAT((CONVERT_TZ(time_login,'GMT','$timezone')),'%H') as login,
        DATE_FORMAT((CONVERT_TZ(time_logout,'GMT','$timezone')),'%H') as logout
      FROM sessions  ses1
      INNER JOIN mac_address 
        ON ses1.mac_id = mac_address.id
      WHERE (mac_address.status<>'noise') 
        AND (sessionid='instore' OR sessionid='passive' OR sessionid='active' OR sessionid='login') 
        AND time_logout IS NOT NULL 
        AND (network_id= $ap_id)
        AND time_login BETWEEN '$start_date' AND '$end_date'
      GROUP BY ses1.mac_id
    ) y
    INNER JOIN network_mac_logins nml
        ON nml.first_logout   < y.max_login 
       AND nml.mac_id=y.unique_mac
    WHERE nml.network_id= $ap_id
      AND nml.first_logout IS NOT NULL
      AND y.max_login IS NOT NULL
) z ON x.hour BETWEEN z.login AND z.logout
GROUP BY x.hour      
ORDER BY x.hour ASC
SQL;
        $table = 'sessions';
        $oModel = new Model(false, $table, 'swarmdata');
        $oDb = $oModel->getDataSource();
        return $oDb->fetchAll($sSQL);
    }
    private function returningByDate($start_date, $end_date, $timezone, $ap_id) {
        $sSQL = <<<SQL
SELECT  
    date(y.max_login) as date,
    COUNT(distinct y.unique_mac) as value
FROM (
    SELECT 
	DISTINCT ses1.mac_id as unique_mac,
	date((CONVERT_TZ(time_login,'GMT','$timezone'))) as max_login 
    FROM sessions  ses1
    INNER JOIN mac_address 
	ON ses1.mac_id = mac_address.id
    WHERE (mac_address.status<>'noise')
      AND (sessionid='instore' OR sessionid='passive' OR sessionid='active' OR sessionid='login') 
      AND time_login IS NOT NULL
      AND (network_id = $ap_id) 
      AND time_login BETWEEN '$start_date' AND '$end_date'  
) y
INNER JOIN network_mac_logins nml
    ON  nml.first_logout < y.max_login 
    AND nml.mac_id=y.unique_mac
WHERE nml.first_logout IS NOT NULL
  AND nml.network_id = $ap_id
  AND y.max_login IS NOT NULL
GROUP BY date       
SQL;
        $table = 'sessions';
        $oModel = new Model(false, $table, 'swarmdata');
        $oDb = $oModel->getDataSource();
        return $oDb->fetchAll($sSQL);
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
            $aByHour = $this->returningByHour($start_date, $end_date, $timezone, $ap_id);
            $aByDate = $this->returningByDate($start_date, $end_date, $timezone, $ap_id);
            return $this->hourlyDailyFormat($aByDate, $aByHour, $data, $params, $start_date, $end_date, '/store/' . __FUNCTION__, 0,'x');
        }
    }

    private function footTrafficByHour($start_date, $end_date, $timezone, $ap_id) {
        $sSQL = <<<SQL
SELECT 
    x.hour,
    COUNT(y.mac_id) as value
FROM(
    SELECT 0 as hour 
    UNION SELECT 1   UNION SELECT 2 	UNION SELECT 3 	
    UNION SELECT 4   UNION SELECT 5	UNION SELECT 6	
    UNION SELECT 7   UNION SELECT 8	UNION SELECT 9	
    UNION SELECT 10  UNION SELECT 11	UNION SELECT 12	
    UNION SELECT 13  UNION SELECT 14	UNION SELECT 15	
    UNION SELECT 16  UNION SELECT 17	UNION SELECT 18	
    UNION SELECT 19  UNION SELECT 20	UNION SELECT 21	
    UNION SELECT 22  UNION SELECT 23
) x
LEFT JOIN
(
    SELECT 
    	ses1.mac_id,DATE_FORMAT(MIN(convert_tz(time_login,'GMT','$timezone')), '%H') AS walk_in,
    	DATE_FORMAT(max(convert_tz(time_logout,'GMT','$timezone')),'%H') AS walk_out
   	FROM sessions ses1
    INNER JOIN mac_address 
    	ON ses1.mac_id = mac_address.id
    WHERE (mac_address.status<>'noise') 
      AND (sessionid='instore' OR sessionid='passive' OR sessionid='active' OR sessionid='login') 
      AND time_logout IS NOT NULL
      AND (network_id=$ap_id)
      AND time_login BETWEEN '$start_date' AND '$end_date'
    GROUP BY ses1.mac_id
) y ON x.hour between walk_in and walk_out 
GROUP BY x.hour
ORDER BY x.hour ASC
SQL;
        $table = 'sessions';
        $oModel = new Model(false, $table, 'swarmdata');
        $oDb = $oModel->getDataSource();
        return $oDb->fetchAll($sSQL);
    }
    private function footTrafficByDate($start_date, $end_date, $timezone, $ap_id) {
        $sSQL = <<<SQL
SELECT 
    DATE(CONVERT_TZ(time_login,'GMT','$timezone')) as date,
    COUNT(DISTINCT ses1.mac_id) as value 
FROM sessions ses1
INNER JOIN mac_address 
    ON ses1.mac_id = mac_address.id
WHERE (mac_address.status<>'noise')
 AND (sessionid='instore' OR sessionid='passive' OR sessionid='active' OR sessionid='login') 
  AND time_logout IS NOT NULL
 AND (network_id= $ap_id) 
 AND time_login BETWEEN '$start_date' AND '$end_date'  
GROUP BY date
SQL;
        $table = 'sessions';
        $oModel = new Model(false, $table, 'swarmdata');
        $oDb = $oModel->getDataSource();
        return $oDb->fetchAll($sSQL);
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
            $aByHour = $this->footTrafficByHour($start_date, $end_date, $timezone, $ap_id);
            $aByDate = $this->footTrafficByDate($start_date, $end_date, $timezone, $ap_id);
            return $this->hourlyDailyFormat($aByDate, $aByHour, $data, $params, $start_date, $end_date, '/store/' . __FUNCTION__, 0,'x');
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
	COUNT(*) as transactions,
	SUM(revenue) as revenue,
	SUM(total_items) as total_items,
	date,
	hour
FROM (
	SELECT
            i.invoice_id as transactions,
	    i.total as revenue,
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
	GROUP BY il.invoice_id
) t2
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
        return $this->format($aRes, $data, $params, $start_date, $end_date, '/store/' . __FUNCTION__, 0, 't2', __FUNCTION__);
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
        return $this->format($aRes, $data, $params, $start_date, $end_date, '/store/' . __FUNCTION__, 0, 't2', __FUNCTION__);
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
        $aRes1 = $this->format($aRes, $data, $params, $start_date, $end_date, '/store/' . __FUNCTION__, 0, 't2', 'revenue');
        $aRes2 = $this->format($aRes, $data, $params, $start_date, $end_date, '/store/' . __FUNCTION__, 0, 't2', 'transactions');
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
        $aRes1 = $this->format($aRes, $data, $params, $start_date, $end_date, '/store/' . __FUNCTION__, 0, 't2', 'total_items');
        $aRes2 = $this->format($aRes, $data, $params, $start_date, $end_date, '/store/' . __FUNCTION__, 0, 't2', 'transactions');
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
