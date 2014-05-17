<?php

App::uses('DBComponent', 'Controller/Component');
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
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            $aRes = $this->iterativeTotals('store', __FUNCTION__, $params);
            $d = $this->countWorkDays($params['start_date'], $params['end_date'], $params['member_id']);
            foreach ($aRes as $k => $v) {
                if (in_array($k, array(
                            'windowConversion',
                            'dwell',
                            'conversionRate',
                            'itemsPerTransaction',
                            'avgTicket',
                                )
                        )
                ) {
                    $aRes[$k] = round($v / $d, 2);
                }
            }
            return $aRes;
        } else {
            $data = $this->api->internalCall('member', 'data', array('member_id' => $params['member_id']));
            $result = array();
            $calls = array(
                array('store', 'walkbys'),
                array('store', 'footTraffic'),
                array('store', 'transactions'),
                array('store', 'revenue'),
                array('store', 'windowConversion'),
                array('store', 'returning'),
                array('store', 'dwell'),
                array('store', 'conversionRate'),
                array('store', 'avgTicket'),
                array('store', 'itemsPerTransaction'),
                array('store', 'totalItems')
            );
            foreach ($calls as $call) {
                $weekday = strtolower(date('l', strtotime($params['start_date'])));
                $isOpen = $data['data'][$weekday . '_open'] != 0 && $data['data'][$weekday . '_close'] != 0;
                $result[$call[1]] = 0;
                if ($isOpen) {
                    $tmp = $this->api->internalCall($call[0], $call[1], $params);
                    switch ($call[1]) {
                        case 'windowConversion':
                        case 'conversionRate':
                        case 'walkbys':
                            $result[$call[1]] = $tmp['data']['totals']['open'];
                            break;
                        case 'footTraffic':
                        case 'returning':
                        case 'dwell':
                            $result[$call[1]] = $tmp['data']['totals']['total'];
                            break;
                        case 'avgTicket':
                        case 'revenue':
                        case 'itemsPerTransaction':
                        case 'transactions':
                        case 'totalItems':
                            if ($data['data']['transactions_while_closed'] == 'no') {
                                $result[$call[1]] = $tmp['data']['totals']['open'];
                            } else {
                                $result[$call[1]] = $tmp['data']['totals']['total'];
                            }
                            break;
                    }
                }
            }
            return $result;
        }
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
            $ap_id = (!empty($data['data']['ap_id'])) ? $data['data']['ap_id'] : 0;
            $timezone = $data['data']['timezone'];
            $factor = $data['data']['traffic_factor'];
            $factor = 1 + ((empty($factor) ? 0 : $factor / 100));
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $table = $this->getSessionsTableName($start_date, $end_date, $params['member_id'], $ap_id);
            $oDb = DBComponent::getInstance($table, 'swarmdataRead');
            $sSQL = <<<SQL
SELECT 
    ROUND(COUNT(walkbys)*$factor) as value, 
    hour, 
    date
FROM(
    SELECT 
        DISTINCT ses1.mac_id as walkbys,        
        DATE_FORMAT(convert_tz(time_login,'GMT', '$timezone'), '%Y-%m-%d') AS date,
        DATE_FORMAT(convert_tz(time_login,'GMT', '$timezone'), '%k') AS hour
    FROM $table ses1
    INNER JOIN mac_address 
        ON ses1.mac_id = mac_address.id
    WHERE ( status !='noise' AND NOISE is false) 
      AND (network_id= $ap_id) 
      AND (sessionid='passerby') 
      AND time_login BETWEEN '$start_date' AND '$end_date'
    GROUP BY ses1.mac_id
) as t2 GROUP BY date ASC, hour ASC             
SQL;
            $aRes = $oDb->fetchAll($sSQL);
            return $this->format($aRes, $data, $params, '/store/' . __FUNCTION__, 0, 't2');
        }
    }

    /**
     * API Method to load traffic data from door sensors
     * @param $params Array containing member_id, start_date and end_date
     * @return array Array of results formatted for display in the dashboard
     */
    public function sensorTraffic($params) {
        // Set validation rules and validate parameters
        $rules = array(
            'member_id' => array('required', 'int'),
            'start_date' => array('required', 'date'),
            'end_date' => array('required', 'date')
        );
        $this->validate($params, $rules);

        // Pass method and parameters to iteration function if the dates are different
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('store', __FUNCTION__, $params);
        }

        // Get member data for member id including member's timezone and traffic factor
        $member_id = $params['member_id'];
        $data = $this->api->internalCall('member', 'data', array('member_id' => $member_id));
        $timezone = $data['data']['timezone'];
        $factor = $data['data']['traffic_factor'];
        $factor = 1 + ((empty($factor) ? 0 : $factor / 100));

        // apply timezone to dates entered and query for sensor detections
        list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
        $table = 'sensor_sessions';
        $oDb = DBComponent::getInstance($table, 'swarmdata');
        $sSQL = <<<SQL
SELECT
    ROUND(COUNT(*)*$factor) AS detect_count,
    DATE_FORMAT(convert_tz(ts,'GMT', '$timezone'), '%Y-%m-%d') AS date,
    DATE_FORMAT(convert_tz(ts,'GMT','$timezone'), '%k:00') AS hour
FROM sensor_sessions
WHERE
    member_id=$member_id AND
    ts BETWEEN '$start_date' AND '$end_date'
GROUP BY date ASC, hour ASC
SQL;
        $aRes = $oDb->fetchAll($sSQL);

        // Loop through results and divide by 2 to account for customers entering and then leaving the store.
        // The exception is 1 for cases where there is one customer currently in the store
        // TODO: migrate this logic to the MySQL query with CEIL(ROUND(COUNT(*)(*$factor)/2) as detect count
        foreach ($aRes[0] as $key => $row) {
            $dCount = intval($row['detect_count']);
            $dCount = ($dCount === 1) ? $dCount : intval($dCount / 2);
            $aRes[0][$key]['detect_count'] = $dCount;
        }

        // return formatted result
        return $this->format($aRes, $data, $params, '/store/' . __FUNCTION__, 0, 0, 'detect_count');
    }
    public function purchaseInfo($params) {
        $rules = array(
            'member_id' => array('required', 'int'),
            'start_date' => array('required', 'date'),
            'end_date' => array('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeQuery('store', __FUNCTION__, $params);
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
            $oDb = DBComponent::getInstance($table, 'pos');
            $aRes = array();
            if (!empty($lightspeed_id)) {
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
	LEFT JOIN invoice_lines il ON i.invoice_id = il.invoice_id
	WHERE i.store_id= $lightspeed_id
	    AND i.completed 
	    AND i.total != 0 
            $register_filter
            $outlet_filter
	    AND i.ts BETWEEN '$start_date' AND '$end_date'
	GROUP BY i.invoice_id
) t2
GROUP BY date ASC, hour ASC             
SQL;
                $aRes = $oDb->fetchAll($sSQL);
            }
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
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('store', __FUNCTION__, $params);
        } else {
            $data = $this->api->internalCall('member', 'data', array('member_id' => $params['member_id']));
            $aRes = $this->api->internalCall('store', 'purchaseInfo', $params);
            $timezone = $data['data']['timezone'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            return $this->format($aRes, $data, $params, '/store/' . __FUNCTION__, 0, 't2', __FUNCTION__);
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
            $aRes = $this->api->internalCall('store', 'purchaseInfo', $params);
            $timezone = $data['data']['timezone'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            return $this->format($aRes, $data, $params, '/store/' . __FUNCTION__, 0, 't2', __FUNCTION__);
        }
    }
    public function totalItems($params) {
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
            $aRes = $this->api->internalCall('store', 'purchaseInfo', $params);
            $timezone = $data['data']['timezone'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            return $this->format($aRes, $data, $params, '/store/' . __FUNCTION__, 0, 't2', 'total_items');
        }
    }

    private function returningByHour($start_date, $end_date, $timezone, $member_id, $ap_id, $factor) {
        $table = $this->getSessionsTableName($start_date, $end_date, $member_id, $ap_id);
        $sSQL = <<<SQL
SELECT
    x.hour,
    ROUND(COUNT(z.login)*$factor) as value 
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
      FROM $table  ses1
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
        $oDb = DBComponent::getInstance($table, 'swarmdataRead');
        return $oDb->fetchAll($sSQL);
    }
    private function returningByDate($date, $data, $timezone, $member_id, $ap_id, $factor) {
        list($start_date, $end_date) = $this->getOpenCloseTimes($date, $data, $timezone);
        $table = $this->getSessionsTableName($start_date, $end_date, $member_id, $ap_id);
        $sSQL = <<<SQL
SELECT  
    date(y.max_login) as date,
    ROUND(COUNT(distinct y.unique_mac)*$factor) as value
FROM (
    SELECT 
	DISTINCT ses1.mac_id as unique_mac,
	date((CONVERT_TZ(time_login,'GMT','$timezone'))) as max_login 
    FROM $table  ses1
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
        $oDb = DBComponent::getInstance($table, 'swarmdataRead');
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
            $ap_id = (!empty($data['data']['ap_id'])) ? $data['data']['ap_id'] : 0;
            $timezone = $data['data']['timezone'];
            $factor = $data['data']['traffic_factor'];
            $factor = 1 + ((empty($factor) ? 0 : $factor / 100));
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $aByHour = $this->returningByHour($start_date, $end_date, $timezone, $params['member_id'], $ap_id, $factor);
            $aByDate = $this->returningByDate($params['start_date'], $data, $timezone, $params['member_id'], $ap_id, $factor);
            return $this->hourlyDailyFormat($aByDate, $aByHour, $data, $params, '/store/' . __FUNCTION__, 0, 'x');
        }
    }
    
    private function footTrafficByHour($start_date, $end_date, $timezone, $member_id, $ap_id, $factor) {
        $table = $this->getSessionsTableName($start_date, $end_date, $member_id, $ap_id);
        $sSQL = <<<SQL
SELECT 
    x.hour,
    ROUND(COUNT(y.mac_id)*$factor) as value
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
   	FROM $table ses1
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
        $oDb = DBComponent::getInstance($table, 'swarmdataRead');
        return $oDb->fetchAll($sSQL);
    }
    private function footTrafficByDate($date, $data, $timezone, $member_id, $ap_id, $factor) {
        list($start_date, $end_date) = $this->getOpenCloseTimes($date, $data, $timezone);
        $table = $this->getSessionsTableName($start_date, $end_date, $member_id, $ap_id);
        $sSQL = <<<SQL
SELECT 
    DATE(CONVERT_TZ(time_login,'GMT','$timezone')) as date,
    ROUND(COUNT(DISTINCT ses1.mac_id)*$factor) as value 
FROM $table ses1
INNER JOIN mac_address 
    ON ses1.mac_id = mac_address.id
WHERE (mac_address.status<>'noise')
 AND (sessionid='instore' OR sessionid='passive' OR sessionid='active' OR sessionid='login') 
  AND time_logout IS NOT NULL
 AND (network_id= $ap_id) 
 AND time_login BETWEEN '$start_date' AND '$end_date'  
GROUP BY date
SQL;
        $oDb = DBComponent::getInstance($table, 'swarmdataRead');
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
            $ap_id = (!empty($data['data']['ap_id'])) ? $data['data']['ap_id'] : 0;
            $timezone = $data['data']['timezone'];
            $factor = $data['data']['traffic_factor'];
            $factor = 1 + ((empty($factor) ? 0 : $factor / 100));
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $aByHour = $this->footTrafficByHour($start_date, $end_date, $timezone, $params['member_id'], $ap_id, $factor);
            $aByDate = $this->footTrafficByDate($params['start_date'], $data, $timezone, $params['member_id'], $ap_id, $factor);
            return $this->hourlyDailyFormat($aByDate, $aByHour, $data, $params, '/store/' . __FUNCTION__, 0, 'x');
        }
    }
    
    private function numDevicesByHour($start_date, $end_date, $timezone, $member_id, $ap_id, $factor) {
        $table = $this->getSessionsTableName($start_date, $end_date, $member_id, $ap_id);
        $sSQL = <<<SQL
SELECT 
    x.hour,
    ROUND(COUNT(y.mac_id)*$factor) as value
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
   	FROM $table ses1
    INNER JOIN mac_address 
    	ON ses1.mac_id = mac_address.id
    WHERE (mac_address.status<>'noise') 
      AND (
         sessionid='passerby' OR 
         sessionid='instore' OR 
         sessionid='passive' OR 
         sessionid='active' OR 
         sessionid='login'
      )       
      AND (network_id=$ap_id)
      AND time_login BETWEEN '$start_date' AND '$end_date'
    GROUP BY ses1.mac_id
) y ON x.hour between walk_in and walk_out 
GROUP BY x.hour
ORDER BY x.hour ASC
SQL;
        $oDb = DBComponent::getInstance($table, 'swarmdataRead');
        return $oDb->fetchAll($sSQL);
    }
    private function numDevicesByDate($date, $data, $timezone, $member_id, $ap_id, $factor) {
        list($start_date, $end_date) = $this->getOpenCloseTimes($date, $data, $timezone);
        $table = $this->getSessionsTableName($start_date, $end_date, $member_id, $ap_id);
        $sSQL = <<<SQL
SELECT 
    DATE(CONVERT_TZ(time_login,'GMT','$timezone')) as date,
    ROUND(COUNT(DISTINCT ses1.mac_id)*$factor) as value 
FROM $table ses1
INNER JOIN mac_address 
    ON ses1.mac_id = mac_address.id
WHERE (mac_address.status<>'noise')
 AND (
     sessionid='passerby' OR 
     sessionid='instore' OR 
     sessionid='passive' OR 
     sessionid='active' OR 
     sessionid='login'
  )   
 AND (network_id= $ap_id) 
 AND time_login BETWEEN '$start_date' AND '$end_date'  
GROUP BY date
SQL;
        $oDb = DBComponent::getInstance($table, 'swarmdataRead');
        return $oDb->fetchAll($sSQL);
    }
    public function numDevices($params) {
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
            $ap_id = (!empty($data['data']['ap_id'])) ? $data['data']['ap_id'] : 0;
            $timezone = $data['data']['timezone'];
            $factor = $data['data']['traffic_factor'];
            $factor = 1 + ((empty($factor) ? 0 : $factor / 100));
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $aByHour = $this->numDevicesByHour($start_date, $end_date, $timezone, $params['member_id'], $ap_id, $factor);
            $aByDate = $this->numDevicesByDate($params['start_date'], $data, $timezone, $params['member_id'], $ap_id, $factor);
            return $this->hourlyDailyFormat($aByDate, $aByHour, $data, $params, '/store/' . __FUNCTION__, 0, 'x');
        }
    }
    private function timeInShopByHour($start_date, $end_date, $timezone, $member_id, $ap_id, $factor) {
        $table = $this->getSessionsTableName($start_date, $end_date, $member_id, $ap_id);
        $sSQL = <<<SQL
SELECT
    x.hour,
    IFNULL(SUM(dwell_time),0) as value 
FROM(
    SELECT 0 as hour 
    UNION SELECT 1   UNION SELECT 2 	UNION SELECT 3 	
    UNION SELECT 4   UNION SELECT 5	UNION SELECT 6	
    UNION SELECT 7   UNION SELECT 8     UNION SELECT 9	
    UNION SELECT 10  UNION SELECT 11	UNION SELECT 12	
    UNION SELECT 13  UNION SELECT 14	UNION SELECT 15	
    UNION SELECT 16  UNION SELECT 17	UNION SELECT 18	
    UNION SELECT 19  UNION SELECT 20	UNION SELECT 21	
    UNION SELECT 22  UNION SELECT 23
) x
LEFT JOIN (
	SELECT 
           DATE_FORMAT((CONVERT_TZ(time_login,'GMT','$timezone')),'%H') as login,
	   ses1.mac_id,
	   SUM(UNIX_TIMESTAMP(time_logout)-UNIX_TIMESTAMP(time_login)) as dwell_time
	FROM $table as ses1
	INNER JOIN mac_address 
		ON ses1.mac_id = mac_address.id
	WHERE status !='noise' 
           AND (            
            sessionid='instore' OR 
            sessionid='passive' OR 
            sessionid='active' OR 
            sessionid='login'
           )  
           AND noise IS false 
	   AND time_logout IS NOT NULL 
	   AND network_id= $ap_id
	   AND time_login BETWEEN '$start_date' AND '$end_date'
	GROUP BY ses1.mac_id
	HAVING 18000 > dwell_time
) z ON z.login = x.hour
GROUP BY x.hour      
ORDER BY x.hour ASC
SQL;
        $oDb = DBComponent::getInstance($table, 'swarmdataRead');
        return $oDb->fetchAll($sSQL);
    }
    private function timeInShopByDate($date, $data, $timezone, $member_id, $ap_id, $factor) {
        list($start_date, $end_date) = $this->getOpenCloseTimes($date, $data, $timezone);
        $table = $this->getSessionsTableName($start_date, $end_date, $member_id, $ap_id);
        $sSQL = <<<SQL
 SELECT SUM(dwell_time) as value
 FROM(
    SELECT 
       ses1.mac_id,
       SUM(UNIX_TIMESTAMP(time_logout)-UNIX_TIMESTAMP(time_login)) as dwell_time
    FROM $table as ses1
    INNER JOIN mac_address 
            ON ses1.mac_id = mac_address.id
    WHERE status !='noise' 
       AND (         
         sessionid='instore' OR 
         sessionid='passive' OR 
         sessionid='active' OR 
         sessionid='login'
       )     
       AND noise IS false 
       AND time_logout IS NOT NULL 
       AND network_id= $ap_id
       AND time_login BETWEEN '$start_date' AND '$end_date'
    GROUP BY ses1.mac_id
    HAVING 18000 > dwell_time
 ) t2
SQL;
        $oDb = DBComponent::getInstance($table, 'swarmdataRead');
        return $oDb->fetchAll($sSQL);
    }
    public function timeInShop($params) {           
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
            $ap_id = (!empty($data['data']['ap_id'])) ? $data['data']['ap_id'] : 0;
            $timezone = $data['data']['timezone'];
            $factor = $data['data']['traffic_factor'];
            $factor = 1 + ((empty($factor) ? 0 : $factor / 100));
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $aByHour = $this->timeInShopByHour($start_date, $end_date, $timezone, $params['member_id'], $ap_id, $factor);
            $aByDate = $this->timeInShopByDate($params['start_date'], $data, $timezone, $params['member_id'], $ap_id, $factor);            
            return $this->hourlyDailyFormat($aByDate, $aByHour, $data, $params, '/store/' . __FUNCTION__, 0, 'x');
        }
    }
    
    //Rates
    public function itemsPerTransaction($params) {
        $tt = $this->api->internalCall('store', 'totalItems', $params);
        $tr = $this->api->internalCall('store', 'transactions', $params);        
        $result = $this->calculate($tt, $tr);        
        $result['options'] = array(
            'endpoint' => '/store/' . __FUNCTION__,
            'member_id' => $params['member_id'],
            'start_date' => $params['start_date'],
            'end_date' => $params['end_date'],
        );
        return $result;
    }
    public function windowConversion($params) {
        $ft = $this->api->internalCall('store', 'footTraffic', $params);
        $nd = $this->api->internalCall('store', 'numDevices', $params);        
        $result = $this->percentify($ft, $nd);
        $result['options'] = array(
            'endpoint' => '/store/' . __FUNCTION__,
            'member_id' => $params['member_id'],
            'start_date' => $params['start_date'],
            'end_date' => $params['end_date'],
        );
        return $result;
    }
    public function avgTicket($params) {
        $re = $this->api->internalCall('store', 'revenue', $params);
        $tr = $this->api->internalCall('store', 'transactions', $params);        
        $result = $this->calculate($re, $tr);        
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
    public function dwell($params) {
        $ts = $this->api->internalCall('store', 'timeInShop', $params);
        $tr = $this->api->internalCall('store', 'footTraffic', $params);                
        $result = $this->calculate($ts, $tr);
        $result['options'] = array(
            'endpoint' => '/store/' . __FUNCTION__,
            'member_id' => $params['member_id'],
            'start_date' => $params['start_date'],
            'end_date' => $params['end_date'],
        );
        return $result;
    }
    
}
