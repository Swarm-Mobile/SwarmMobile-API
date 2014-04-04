<?php

App::uses('APIComponent', 'Controller/Component');
App::uses('Model', 'Model');

class MemberComponent extends APIComponent {

    public function members($params) {
        $rules = array('member_id' => array('required', 'int'));
        $this->validate($params, $rules);
        $member_id = $params['member_id'];
        $table = 'exp_mx_members_lead';
        $oModel = new Model(false, $table, 'ee');
        $oDb = $oModel->getDataSource();
        $sSQL = "SELECT member_id FROM $table WHERE lead_id = :member_id";
        $bind = array('member_id' => $member_id);
        $result = $oDb->fetchAll($sSQL, $bind);
        $tmp = array('members' => array());
        foreach ($result as $row) {
            $tmp['members'][] = (int) $row[$table]['member_id'];
        }
        return $tmp;
    }

    public function accessPoint($params) {
        $rules = array('member_id' => array('required', 'int'));
        $this->validate($params, $rules);
        $member_id = $params['member_id'];
        $table = 'exp_member_data';
        $oModel = new Model(false, $table, 'ee');
        $oDb = $oModel->getDataSource();
        $sSQL = "SELECT m_field_id_20 FROM $table WHERE member_id = :member_id";
        $bind = array('member_id' => $member_id);
        $result = $oDb->fetchAll($sSQL, $bind);
        $tmp = array('accessPoint' => array());
        foreach ($result as $row) {
            $tmp['accessPoint'][] = (int) $row[$table]['m_field_id_20'];
        }
        return $tmp;
    }

    public function data($params) {
        $rules = array('member_id' => array('required', 'int'));
        $this->validate($params, $rules);
        $member_id = $params['member_id'];
        $table = 'exp_member_data';
        $oModel = new Model(false, $table, 'ee');
        $oDb = $oModel->getDataSource();
        $sSQL = <<<SQL
SELECT 
    m_field_id_20 as ap_id,
    m_field_id_21 as timezone,
    m_field_id_22 as store_open,
    m_field_id_23 as store_close,
    m_field_id_28 as lightspeed_id,
    m_field_id_34 as monday_open,
    m_field_id_35 as monday_close,
    m_field_id_36 as tuesday_open,
    m_field_id_37 as tuesday_close,
    m_field_id_38 as wednesday_open,
    m_field_id_39 as wednesday_close,
    m_field_id_40 as thursday_open,
    m_field_id_41 as thursday_close,
    m_field_id_42 as friday_open,
    m_field_id_43 as friday_close,
    m_field_id_44 as saturday_open,
    m_field_id_45 as saturday_close,
    m_field_id_46 as sunday_open,
    m_field_id_47 as sunday_close,
    m_field_id_51 as network_provider,
    m_field_id_54 as register_filter,
    m_field_id_55 as outlet_filter,
    m_field_id_56 as country,
    m_field_id_57 as nightclub_hours,
    m_field_id_58 as traffic_factor,
    m_field_id_106 as nightclub_hours_location
FROM $table  
WHERE member_id = :member_id
SQL;
        $bind = array('member_id' => $member_id);
        $result = $oDb->fetchAll($sSQL, $bind);
        $tmp = array('data' => array());
        foreach ($result[0][$table] as $k => $v) {
            $tmp['data'][$k] = $v;
        }
        if (empty($tmp['data']['network_provider'])) {
            $tmp['data']['network_provider'] = 'gp';
        }
        if (empty($tmp['data']['timezone'])) {
            $tmp['data']['timezone'] = 'America/Los_Angeles';
        }
        foreach (array('open', 'close') as $state) {
            if (empty($tmp['data']['store_' . $state])) {
                $tmp['data']['store_' . $state] = $state == 'open' ? '09:00' : '21:00';
            }
            foreach ($this->weekdays as $day) {
                $daystate = $day . '_' . $state;
                $val = isset($tmp['data'][$daystate]) ? $tmp['data'][$daystate] : null;
                if (is_null($val) || trim($val) === '') {
                    $tmp['data'][$daystate] = $tmp['data']['store_' . $state];
                }
            }
        }
        return $tmp;
    }

    public function settings($params) {
        
    }

}
