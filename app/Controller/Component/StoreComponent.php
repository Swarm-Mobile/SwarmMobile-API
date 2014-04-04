<?php

App::uses('APIComponent', 'Controller/Component');

class StoreComponent extends APIComponent {    
    public function totals($params){
        $rules = array(
            'member_id' => array('required','int'),
            'start_date' => array('required','date'),
            'end_date' => array('required','date')
        );
        $this->validate($rules, $params);        
        $result = array();
        $result['avgTicket'] = $this->api->internalCall('store', 'avgTicket', $params);
        //...
        return $result;
    }
    public function walkbys($params){
        $rules = array(
            'member_id' => array('required','int'),
            'start_date' => array('required','date'),
            'end_date' => array('required','date')
        );
        $this->validate($rules, $params);  
        
        $start_date     = $params['start_date'].' 00:00:00';
        $end_date       = $params['end_date'].' 23:59:59';        
        $group_by       = $this->getGroupByType($params);
        $data           = $this->api->internalCall('member','data', $params);
        $ap_id          = $data['data']['ap_id'];
        $timezone       = $data['data']['timezone'];        
        $timezone       = self::getLocalTimezone($timezone);    
        $timezone       = $timezone->timezone;
        $table  = 'sessions';
        $oModel = new Model(false, $table, 'swarmdata');
        $oDb    = $oModel->getDataSource();        
        
        $sSQL = <<<SQL
SELECT 
    count(walkbys) as walkbys, 
    hours as hour_of_day, 
    date as dt 
FROM(
    SELECT 
        DISTINCT sessions.mac_id as walkbys,
        DATE( convert_tz(time_login,'GMT',:timezone)) AS date,
        DATE_FORMAT(convert_tz(time_login,'GMT', :timezone), '%k') AS hours
    FROM sessions
    INNER JOIN mac_address 
        ON sessions.mac_id = mac_address.id
    WHERE ( status !='noise' AND NOISE is false) 
      AND (network_id=:ap_id) 
      AND (sessionid='passerby') 
      AND time_login BETWEEN :start_date AND :end_date
    GROUP BY sessions.mac_id
) as t2 GROUP BY $group_by             
SQL;
        $bind               = array();
        $bind['timezone']   = $timezone;
        $bind['ap_id']      = $ap_id;
        $bind['start_date'] = $start_date;
        $bind['end_date']   = $end_date;        
        $result             = $oDb->fetchAll($sSQL, $bind);
        return $result;
    }
    
    public function shoppers($params){}
    public function transactions($params){}
    public function revenue($params){}
    public function windowConversion($params){}
    public function conversionRate($params){}
    public function avgTicket($params){}
    public function openHours($params){}
}
