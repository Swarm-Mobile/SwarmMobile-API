<?php

App::uses('APIComponent', 'Controller/Component');

class StoreComponent extends APIComponent {

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
			array('store', 'windowConversion'),
			array('store', 'conversionRate'),
		);
		foreach ($calls as $call) {
			$tmp = $this->api->internalCall($call[0], $call[1], $params);
			$result[$call[1]] = $tmp[$call[1]];
		}
		return $result;
	}

	public function walkbys($params) {
		$rules = array(
			'member_id' => array('required', 'int'),
			'start_date' => array('required', 'date'),
			'end_date' => array('required', 'date')
		);
		$this->validate($rules, $params);
		if ($params['start_date'] != $params['end_date']) {
			return $this->iterativeCall('store', 'walkbys', $params);
		} else {
			$data = $this->api->internalCall('member', 'data', $params);
			$ap_id = $data['data']['ap_id'];
			$timezone = $data['data']['timezone'];
			list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
			$table = 'sessions';
			$oModel = new Model(false, $table, 'swarmdata');
			$oDb = $oModel->getDataSource();

			$sSQL = <<<SQL
SELECT 
    COUNT(walkbys) as walkbys, 
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
			$cResult = array();
			foreach ($aRes as $oRow) {
				$weekday = strtolower(date('l', strtotime($oRow['t2']['date'])));
				$date = $oRow['t2']['date'];
				$hour = $oRow['t2']['hour'];
				$cWalkbys = (int) $oRow[0]['walkbys'];
				if (
						(int) $hour >= (int) strstr($data['data'][$weekday . '_open'], ':', true) &&
						(int) $hour <= (int) strstr($data['data'][$weekday . '_close'], ':', true)
				) {
					$cResult['breakdown'][$date]['hours'][$hour]['open'] = true;
					$cResult['breakdown'][$date]['totals']['open'] += $cWalkbys;
					$cResult['totals']['open'] += $cWalkbys;
				} else {
					$cResult['breakdown'][$date]['hours'][$hour]['open'] = false;
					$cResult['breakdown'][$date]['totals']['close'] += $cWalkbys;
					$cResult['totals']['close'] += $cWalkbys;
				}
				$cResult['breakdown'][$date]['totals']['total'] += $cWalkbys;
				$cResult['breakdown'][$date]['hours'][$hour]['total'] += $cWalkbys;
				$cResult['totals']['total'] += $cWalkbys;
			}
			$cResult['options'] = array(
				'endpoint' => '/store/walkbys',
				'member_id' => $params['member_id'],
				'start_date' => $params['start_date'],
				'end_date' => $params['end_date'],
			);
			return $this->fillBlanks($cResult);
		}
	}

	public function footTraffic($params) {
		$rules = array(
			'member_id' => array('required', 'int'),
			'start_date' => array('required', 'date'),
			'end_date' => array('required', 'date')
		);
		$this->validate($rules, $params);

		$data = $this->api->internalCall('member', 'data', $params);
		$ap_id = $data['data']['ap_id'];
		$timezone = $data['data']['timezone'];

		list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
		$store_open_compare = $this->storeOpenCompare($data, $timezone);

		$group_by = $this->getGroupByType($params);
		$table = 'sessions';
		$oModel = new Model(false, $table, 'swarmdata');
		$oDb = $oModel->getDataSource();

		$sSQL = <<<SQL
SELECT 
    COUNT(foot_traffic) as foot_traffic, 
    hours, 
    date
FROM(
    SELECT 
        DISTINCT sessions.mac_id as foot_traffic,
        DATE(CONVERT_TZ(time_login,'GMT', '$timezone' )) AS date,
        DATE_FORMAT(CONVERT_TZ(time_login,'GMT', '$timezone' ), '%k') AS hours
    FROM sessions
    INNER JOIN mac_address 
        ON (sessions.mac_id = mac_address.id)
    WHERE (mac_address.status<>'noise')
      AND (time_logout IS NOT NULL)
      AND $store_open_compare  
      AND sessionid IN ('instore','passive','active','login')
      AND network_id = $ap_id
      AND time_login BETWEEN '$start_date' AND '$end_date'
) as t2 GROUP BY $group_by                     
SQL;
		$aRes = $oDb->fetchAll($sSQL);
		$result = array('footTraffic' => 0, 'breakdown' => array());

		foreach ($aRes as $oRow) {
			$result['footTraffic'] += (int) $oRow[0]['foot_traffic'];
			$result['breakdown'][$oRow['t2'][$group_by]] = (int) $oRow[0]['foot_traffic'];
		}

		$result['options'] = array(
			'member_id' => $params['member_id'],
			'start_date' => $params['start_date'],
			'end_date' => $params['end_date'],
			'group_by' => $group_by,
		);
		return $result;
	}

	public function transactions($params) {
		$rules = array(
			'member_id' => array('required', 'int'),
			'start_date' => array('required', 'date'),
			'end_date' => array('required', 'date')
		);
		$this->validate($rules, $params);

		$data = $this->api->internalCall('member', 'data', $params);
		$timezone = $data['data']['timezone'];
		$lightspeed_id = $data['data']['lightspeed_id'];
		list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);

		$group_by = $this->getGroupByType($params);
		$table = 'invoices';
		$oModel = new Model(false, $table, 'pos');
		$oDb = $oModel->getDataSource();

		$sSQL = <<<SQL
SELECT 
    COUNT(invoice_id) as transactions,
    DATE_FORMAT(CONVERT_TZ(invoices.ts,'GMT','$timezone'),'%Y-%m-%d' ) AS date,
    DATE_FORMAT(CONVERT_TZ(ts,'GMT','$timezone'), '%k') AS hours
FROM $table
WHERE store_id= $lightspeed_id
    AND completed 
    AND invoices.total != 0 
    AND {$this->registerFilter($data)}
	{$this->outletFilter($data)}
        invoices.ts BETWEEN '$start_date' AND '$end_date'
GROUP BY $group_by                
SQL;
		$aRes = $oDb->fetchAll($sSQL);
		$result = array('transactions' => 0, 'breakdown' => array()
		);
		foreach ($aRes as $oRow) {
			$result['transactions'] += (int) $oRow[0]['transactions'];
			$result['breakdown'][$oRow[0][$group_by]] = (int) $oRow[0]['transactions'];
		}

		$result['options'] = array(
			'member_id' => $params['member_id'],
			'start_date' => $params['start_date'],
			'end_date' => $params['end_date'],
			'group_by' => $group_by,
		);
		return $result;
	}

	public function revenue($params) {
		$rules = array(
			'member_id' => array('required', 'int'),
			'start_date' => array('required', 'date'),
			'end_date' => array('required', 'date')
		);
		$this->validate($rules, $params);

		$data = $this->api->internalCall('member', 'data', $params);
		$timezone = $data['data']['timezone'];
		$lightspeed_id = $data['data']['lightspeed_id'];
		list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);

		$group_by = $this->getGroupByType($params);
		$table = 'invoices';
		$oModel = new Model(false, $table, 'pos');
		$oDb = $oModel->getDataSource();

		$sSQL = <<<SQL
SELECT 
    SUM(total) as revenue,
    DATE_FORMAT(CONVERT_TZ(invoices.ts,'GMT','$timezone'),'%Y-%m-%d' ) AS date,
    DATE_FORMAT(CONVERT_TZ(ts,'GMT','$timezone'), '%k:00') AS hours
FROM $table
WHERE store_id= $lightspeed_id
    AND completed 
    AND invoices.total != 0 
    AND {$this->registerFilter($data)}
	{$this->outletFilter($data)}
        invoices.ts BETWEEN '$start_date' AND '$end_date'
GROUP BY $group_by                
SQL;
		$aRes = $oDb->fetchAll($sSQL);
		$result = array('revenue' => 0, 'breakdown' => array()
		);
		foreach ($aRes as $oRow) {
			$result['revenue'] += (int) $oRow[0]['revenue'];
			$result['breakdown'][$oRow[0][$group_by]] = (int) $oRow[0]['revenue'];
		}

		$result['options'] = array(
			'member_id' => $params['member_id'],
			'start_date' => $params['start_date'],
			'end_date' => $params['end_date'],
			'group_by' => $group_by,
		);
		return $result;
	}

	public function avgTicket($params) {
		$rules = array(
			'member_id' => array('required', 'int'),
			'start_date' => array('required', 'date'),
			'end_date' => array('required', 'date')
		);
		$this->validate($rules, $params);

		$data = $this->api->internalCall('member', 'data', $params);
		$timezone = $data['data']['timezone'];
		$lightspeed_id = $data['data']['lightspeed_id'];
		list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);

		$group_by = $this->getGroupByType($params);
		$table = 'invoices';
		$oModel = new Model(false, $table, 'pos');
		$oDb = $oModel->getDataSource();

		$sSQL = <<<SQL
SELECT 
    AVG(total) AS avgTicket,
    DATE_FORMAT(CONVERT_TZ(invoices.ts,'GMT','$timezone'),'%Y-%m-%d' ) AS date,
    DATE_FORMAT(CONVERT_TZ(ts,'GMT','$timezone'), '%k:00') AS hours
FROM $table
WHERE store_id= $lightspeed_id
    AND completed 
    AND invoices.total != 0 
    AND {$this->registerFilter($data)}
	{$this->outletFilter($data)}
        invoices.ts BETWEEN '$start_date' AND '$end_date'
GROUP BY $group_by                
SQL;
		$aRes = $oDb->fetchAll($sSQL);
		$result = array('avgTicket' => 0, 'breakdown' => array()
		);
		foreach ($aRes as $oRow) {
			$result['avgTicket'] += (int) $oRow[0]['avgTicket'];
			$result['breakdown'][$oRow[0][$group_by]] = (int) $oRow[0]['avgTicket'];
		}

		$result['options'] = array(
			'member_id' => $params['member_id'],
			'start_date' => $params['start_date'],
			'end_date' => $params['end_date'],
			'group_by' => $group_by,
		);
		return $result;
	}

	public function windowConversion($params) {
		$ft = $this->api->internalCall('store', 'footTraffic', $params);
		$wb = $this->api->internalCall('store', 'walkbys', $params);
		$result = array(
			'windowConversion' => round($ft['footTraffic'] / $wb['walkbys'], 2),
			'breakdown' => array()
		);
		foreach ($ft['breakdown'] as $k => $v) {
			$result['breakdown'][$k] = round($ft['breakdown'][$k] / $wb['breakdown'][$k], 2);
		}
		return $result;
	}

	public function conversionRate($params) {
		$tr = $this->api->internalCall('store', 'transactions', $params);
		$ft = $this->api->internalCall('store', 'footTraffic', $params);
		$result = array(
			'conversionRate' => round($tr['transactions'] / $ft['footTraffic'], 2),
			'breakdown' => array()
		);
		foreach ($ft['breakdown'] as $k => $v) {
			$result['breakdown'][$k] = round($tr['breakdown'][$k] / $ft['breakdown'][$k], 2);
		}
		return $result;
	}

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
		return array('openHours' => $return);
	}

}
