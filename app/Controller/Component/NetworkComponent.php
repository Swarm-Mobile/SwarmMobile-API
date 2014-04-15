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
    DISTINCT COUNT(logins.mac_id) as value, 
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
    COUNT(emailId) as value, 
    DATE_FORMAT(convert_tz(time,'GMT', '$timezone'), '%Y-%m-%d') AS date,
    DATE_FORMAT(convert_tz(time,'GMT', '$timezone'), '%k') AS hour
FROM `ws_user_profile`
WHERE storeId= $member_id  
  AND emailId != 'Guest'    
  AND time BETWEEN '$start_date' AND '$end_date'
GROUP BY hour
SQL;
            $aRes = $oDb->fetchAll($sSQL);            
            return $this->format($aRes, $data, $params, $start_date, $end_date, '/network/' . __FUNCTION__, 0, 0);
        }
    }

    public function emails($params) {
        
    }

    public function websites($params) {
        
    }

}
