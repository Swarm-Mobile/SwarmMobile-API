<?php

App::uses('APIComponent', 'Controller/Component');
App::uses('Model', 'Model');

class MemberComponent extends APIComponent {
    
    public function members($params){
        $rules = array('member_id'=>array('required','int'));
        $this->validate($params, $rules);    
        $member_id  = $params['member_id'];        
        $table      = 'exp_mx_members_lead';
        $oModel     = new Model(false, $table, 'ee');
        $oDb        = $oModel->getDataSource();
        $sSQL       = "SELECT member_id FROM $table WHERE lead_id = :member_id";
        $bind       = array('member_id'=>$member_id);                    
        $result     = $oDb->fetchAll($sSQL, $bind);
        $tmp        = array('members'=>array());
        foreach($result as $row){            
            $tmp['members'][] = (int)$row[$table]['member_id'];
        }
        return $tmp;
    }
    
    public function accessPoint($params){
        $rules = array('member_id'=>array('required','int'));
        $this->validate($params, $rules);    
        $member_id  = $params['member_id'];        
        $table      = 'exp_member_data';
        $oModel     = new Model(false, $table, 'ee');
        $oDb        = $oModel->getDataSource();
        $sSQL       = "SELECT m_field_id_20 FROM $table WHERE member_id = :member_id";
        $bind       = array('member_id'=>$member_id);                    
        $result     = $oDb->fetchAll($sSQL, $bind);
        $tmp        = array('accessPoint'=>array());
        foreach($result as $row){            
            $tmp['accessPoint'][] = (int)$row[$table]['m_field_id_20'];
        }
        return $tmp;
    }
    
    public function data($params){
        $rules = array('member_id'=>array('required','int'));
        $this->validate($params, $rules);    
        $member_id  = $params['member_id'];        
        $table      = 'exp_member_data';
        $oModel     = new Model(false, $table, 'ee');
        $oDb        = $oModel->getDataSource();
        $sSQL       = <<<SQL
SELECT 
    m_field_id_20 as ap_id,
    m_field_id_21 as timezone,
    m_field_id_28 as lightspeed_id
FROM $table  
WHERE member_id = :member_id
SQL;
        $bind       = array('member_id'=>$member_id);                    
        $result     = $oDb->fetchAll($sSQL, $bind);
        $tmp        = array('data'=>array());
        foreach($result[0][$table] as $k=>$v){            
            $tmp['data'][$k] = $v;
        }
        return $tmp;        
    }
    
    public function settings($params){}    
    
}
