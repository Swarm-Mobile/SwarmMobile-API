<?php

App::uses('APIComponent', 'Controller/Component');

class NetworkComponent extends APIComponent {

    public function wifiConnections($params) {
        $rules = array(
            'member_id' => array('required', 'int'),
            'start_date' => array('required', 'date'),
            'end_date' => array('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('network', __FUNCTION__, $params);
        } else {
            $data = $this->api->internalCall('member', 'data', array('member_id' => $params['member_id']));
            $ap_id = $data['data']['ap_id'];
            $timezone = $data['data']['timezone'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $table = 'logins';
            $oModel = new Model(false, $table, 'swarmdata');
            $oDb = $oModel->getDataSource();
            $sSQL = <<<SQL
SELECT 
    COUNT(DISTINCT logins.mac_id) as value, 
    DATE_FORMAT(convert_tz(time_login,'GMT', '$timezone'), '%Y-%m-%d') AS date,
    DATE_FORMAT(convert_tz(time_login,'GMT', '$timezone'), '%k') AS hour
FROM `logins` 
WHERE time_logout IS NOT NULL 
  AND network_id = $ap_id  
  AND time_login BETWEEN '$start_date' AND '$end_date'
GROUP BY date ASC, hour ASC
SQL;
            $aRes = $oDb->fetchAll($sSQL);
            return $this->format($aRes, $data, $params, $start_date, $end_date, '/network/' . __FUNCTION__, 0, 0);
        }
    }

    public function emailsCaptured($params) {
        $rules = array(
            'member_id' => array('required', 'int'),
            'start_date' => array('required', 'date'),
            'end_date' => array('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('network', __FUNCTION__, $params);
        } else {
            $data = $this->api->internalCall('member', 'data', array('member_id' => $params['member_id']));
            $timezone = $data['data']['timezone'];
            $member_id = $params['member_id'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $table = 'ws_user_profile';
            $oModel = new Model(false, $table, 'ee');
            $oDb = $oModel->getDataSource();
            $sSQL = <<<SQL
SELECT 
    COUNT(DISTINCT emailId) as value, 
    DATE_FORMAT(convert_tz(time,'GMT', '$timezone'), '%Y-%m-%d') AS date,
    DATE_FORMAT(convert_tz(time,'GMT', '$timezone'), '%k') AS hour
FROM `ws_user_profile`
WHERE storeId= $member_id  
  AND emailId != 'Guest'    
  AND emailId != ''
  AND emailId IS NOT NULL 
  AND time BETWEEN '$start_date' AND '$end_date'
GROUP BY hour
SQL;
            $aRes = $oDb->fetchAll($sSQL);
            return $this->format($aRes, $data, $params, $start_date, $end_date, '/network/' . __FUNCTION__, 0, 0);
        }
    }

    public function emails($params) {
        $rules = array(
            'member_id' => array('required', 'int'),
            'start_date' => array('required', 'date'),
            'end_date' => array('required', 'date')
        );
        $this->validate($params, $rules);
        $data = $this->api->internalCall('member', 'data', array('member_id' => $params['member_id']));
        $timezone = $data['data']['timezone'];
        $member_id = $params['member_id'];
        list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
        $table = 'ws_user_profile';
        $oModel = new Model(false, $table, 'ee');
        $oDb = $oModel->getDataSource();
        $sSQL = <<<SQL
SELECT 
    emailId,
    convert_tz(time,'GMT', '$timezone') AS date    
FROM `ws_user_profile`
WHERE storeId= $member_id  
  AND emailId != 'Guest'    
  AND emailId != ''
  AND emailId IS NOT NULL
  AND time BETWEEN '$start_date' AND '$end_date'
SQL;
        $aRes = $oDb->fetchAll($sSQL);
        $result = array();
        $result['data'] = array();
        foreach($aRes as $oRow){            
            $result['data'][] = array(
                'email'=>$oRow['ws_user_profile']['emailId'], 
                'time'=>$oRow[0]['date']
            );
        }        
        $result['options'] = array(
            'endpoint' => '/network/websites',
            'member_id' => $params['member_id'],
            'start_date' => $params['start_date'],
            'end_date' => $params['end_date'],
        );
        return $result;
    }

    public function websites($params) {
        $rules = array(
            'member_id' => array('required', 'int'),
            'start_date' => array('required', 'date'),
            'end_date' => array('required', 'date')
        );
        $this->validate($params, $rules);
        $data = $this->api->internalCall('member', 'data', array('member_id' => $params['member_id']));
        $timezone = $data['data']['timezone'];        
        $ap_id = $data['data']['ap_id'];
        list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
        $table = 'analytics';
        $oModel = new Model(false, $table, 'swarmdata');
        $oDb = $oModel->getDataSource();
        $sSQL = <<<SQL
SELECT
    domain,
    COUNT(*) as count
FROM analytics 
WHERE
    netid = $ap_id AND 
    domain IS NOT NULL AND
    domain !='' AND
    time BETWEEN '$start_date' AND '$end_date'
GROUP BY domain 
ORDER BY count DESC
SQL;
        $aRes = $oDb->fetchAll($sSQL);
        $result = array();
        $result['data'] = array();
        foreach($aRes as $oRow){            
            $result['data'][] = array(
                'domain'=>$oRow['analytics']['domain'], 
                'count'=>$oRow[0]['count']
            );
        }        
        $result['options'] = array(
            'endpoint' => '/network/websites',
            'member_id' => $params['member_id'],
            'start_date' => $params['start_date'],
            'end_date' => $params['end_date'],
        );
        return $result;
    }

}
