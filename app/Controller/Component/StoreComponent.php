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
        $calls = array(
            array('store','walkbys'),
            array('store','footTraffic'),            
            array('store','transactions'),            
            array('store','revenue'),            
            array('store','windowConversion'),            
            array('store','conversionRate'),            
            array('store','avgTicket'),            
            array('store','openHours'),            
        );
        foreach($calls as $call){            
            $tmp = $this->api->internalCall($call[0], $call[1], $params);            
            $result[$call[1]] = $tmp[$call[1]];
        }        
        return $result;
    }
    
    public function walkbys($params){
        $rules = array(
            'member_id' => array('required','int'),
            'start_date' => array('required','date'),
            'end_date' => array('required','date')
        );
        $this->validate($rules, $params);  
 
        $data       = $this->api->internalCall('member','data', $params);
        $ap_id      = $data['data']['ap_id'];
        $timezone   = $data['data']['timezone'];    
        
        list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);               
        
        $group_by   = $this->getGroupByType($params);   
        $table      = 'sessions';
        $oModel     = new Model(false, $table, 'swarmdata');
        $oDb        = $oModel->getDataSource();        
        
        $sSQL = <<<SQL
SELECT 
    COUNT(walkbys) as walkbys, 
    hours, 
    date
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
        $aRes               = $oDb->fetchAll($sSQL, $bind);
        $result             = array('walkbys'=>0, 'breakdown'=>array());
        
        foreach($aRes as $oRow){
            $result['walkbys'] += (int)$oRow[0]['walkbys'];
            $result['breakdown'][$oRow['t2'][$group_by]] = (int)$oRow[0]['walkbys'];
        }
        
        $result['options'] = 
            array(
                'member_id'=>$params['member_id'],
                'start_date'=>$params['start_date'],
                'end_date'=>$params['end_date'],
                'group_by'=>$group_by,
            );
        
        return $result;
    }
    
    public function footTraffic($params){
        $rules = array(
            'member_id' => array('required','int'),
            'start_date' => array('required','date'),
            'end_date' => array('required','date')
        );
        $this->validate($rules, $params);  
         
        $data       = $this->api->internalCall('member','data', $params);
        $ap_id      = $data['data']['ap_id'];
        $timezone   = $data['data']['timezone'];    
        
        list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);               
        $store_open_compare = $this->storeOpenCompare($data, $timezone);
        
        $group_by   = $this->getGroupByType($params);   
        $table      = 'sessions';
        $oModel     = new Model(false, $table, 'swarmdata');
        $oDb        = $oModel->getDataSource();        
        
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
        $aRes   = $oDb->fetchAll($sSQL);        
        $result = array('footTraffic'=>0, 'breakdown'=>array());
        
        foreach($aRes as $oRow){
            $result['footTraffic'] += (int)$oRow[0]['foot_traffic'];
            $result['breakdown'][$oRow['t2'][$group_by]] = (int)$oRow[0]['foot_traffic'];
        }
        
        $result['options'] = 
            array(
                'member_id'=>$params['member_id'],
                'start_date'=>$params['start_date'],
                'end_date'=>$params['end_date'],
                'group_by'=>$group_by,
            );                
        return $result;
    }
    
    public function transactions($params){}
    public function revenue($params){}
    public function windowConversion($params){}
    public function conversionRate($params){}
    public function avgTicket($params){}
    public function openHours($params){}
}
