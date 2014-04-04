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
        $sSQL       = "SELECT member_id FROM $table WHERE lead_id = $member_id";                
        $result     = $oModel->query($sSQL);
        $tmp        = array('members'=>array());
        foreach($result as $row){            
            $tmp['members'][] = (int)$row[$table]['member_id'];
        }
        return $tmp;
    }
    public function settings($params){}    
    
}
