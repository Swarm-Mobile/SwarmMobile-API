<?php
App::uses('APIComponent', 'Controller/Component');
class ConsumerComponent extends APIComponent {
    public function location($params){
        $rules = array(
            'location_id' => array('required', 'int'),
            'start_date' => array('required', 'date'),
            'end_date' => array('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('location', __FUNCTION__, $params);
        } else {
            $data = $this->api->internalCall('location', 'data', array('location_id' => $params['location_id']));
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
        DATE_FORMAT(convert_tz(time_login,'GMT', '$timezone'), '%Y-%m-%d') AS date,
        DATE_FORMAT(convert_tz(time_login,'GMT', '$timezone'), '%k') AS hour
    FROM sessions
    INNER JOIN mac_address 
        ON sessions.mac_id = mac_address.id
    WHERE ( status !='noise' AND NOISE is false) 
      AND (network_id= $ap_id) 
      AND (sessionid='passerby') 
      AND time_login BETWEEN '$start_date' AND '$end_date'
    GROUP BY sessions.mac_id
) as t2 GROUP BY date ASC, hour ASC             
SQL;
            $aRes = $oDb->fetchAll($sSQL);
            return $this->format($aRes, $data, $params, $start_date, $end_date, '/location/' . __FUNCTION__, 0, 't2');
        }
    }
}
