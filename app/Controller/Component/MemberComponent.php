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
    m_field_id_104 as no_rollups,
    m_field_id_105 as no_cache,
    m_field_id_106 as nightclub_hours_location,
    m_field_id_123 as transactions_while_closed
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

    /**
     * Update member preferences
     * 
     * @param Array
     * @return 
     */
    public function updatePreferences($params) {
        $this->verify($params);
        $member_id = $params['member_id'];
        $username = $params['username'];

        $update = $updateMem = array();
        unset($params['uuid']);
        unset($params['member_id']);
        unset($params['username']);
        $fields = $this->getMemberFields();

        foreach ($params as $key => $val) {
            if ($key == 'email' || $key == 'screen_name') {
                $updateMem[$key] = $val;
            } elseif (array_key_exists($key, $fields)) {
                $update[$key] = $val;
            } elseif ($dbKey = array_search($key, $fields)) {
                $update[$dbKey] = $val;
            }
        }

        $sSQL = 'UPDATE exp_member_data SET ';
        if (!empty($update)) {
            $table = 'exp_member_data';
            $oModel = new Model(false, $table, 'ee');
            $oDb = $oModel->getDataSource();
            $flag = false;
            $binds = array();
            foreach ($update as $key => $val) {
                if (!$flag) {
                    $sSQL .= $key . '= :' . $key;
                    $binds[':' . $key] = "$val";
                    $flag = true;
                } else {
                    $sSQL .= ',' . $key . '= :' . $key;
                    $binds[':' . $key] = "$val";
                }
            }

            $sSQL .= ' WHERE member_id=' . $member_id;

            $oDb->query($sSQL, $binds);
        }

        if (!empty($updateMem) && isset($username)) {
            $table = 'exp_members';
            $oModel = new Model(false, $table, 'ee');
            $oDb = $oModel->getDataSource();
            $binds = array();
            $sSQL = "UPDATE exp_members SET ";
            $flag = false;
            foreach ($updateMem as $key => $val) {
                if (!$flag) {
                    $sSQL .= $key . '= :' . $key;
                    $binds[':' . $key] = "$val";
                    $flag = true;
                } else {
                    $sSQL .= ',' . $key . '= :' . $key;
                    $binds[':' . $key] = "$val";
                }
            }

            $sSQL .= ' WHERE username=:username';
            $binds[':username'] = $username;
            $oDb->query($sSQL, $binds);
        }
        return array('success' => 'Data updated successfully');
    }

    /**
     * Get member preferences 
     * 
     * @param Array post data
     * @return Array
     */
    public function getPreferences($params) {
        $this->verify($params);
        $member_id = $params['member_id'];
        $username = $params['username'];
        $table = 'exp_member_data';
        $oModel = new Model(false, $table, 'ee');
        $oDb = $oModel->getDataSource();
        $sSQL = <<<SQL
SELECT 
    m_field_id_5 as address,
    m_field_id_7 as city,
    m_field_id_56 as country,
    m_field_id_48 as industry,
    m_field_id_125 as receive_daily,
    m_field_id_126 as receive_weekly,
    m_field_id_127 as receive_monthly,
    m_field_id_124 as white_label_dashboard,
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
    m_field_id_21 as store_timezone,
    m_field_id_60 as unit_of_measurement,
    m_field_id_61 as size_of_store,
    m_field_id_62 as distance_to_front_door,
    m_field_id_63 as distance_to_left_wall,
    m_field_id_64 as distance_to_right_wall,
    m_field_id_65 as distance_to_back_wall,
    m_field_id_66 as shape_of_store,
    m_field_id_69 as estimated_daily_foot_traffic,
    m_field_id_51 as network_provider,
    m_field_id_72 as guest_wifi,
    m_field_id_123 as transactions_while_closed
FROM $table  
WHERE member_id = :member_id
SQL;
        $bind = array('member_id' => $member_id);
        $result = $oDb->fetchAll($sSQL, $bind);
        $ret = array('data' => array());
        foreach ($result[0][$table] as $k => $v) {
            $ret['data'][$k] = $v;
        }

        if ($params['username']) {
            $table = 'exp_members';
            $oModel = new Model(false, $table, 'ee');
            $oDb = $oModel->getDataSource();
            $sSQL = "SELECT screen_name, email FROM exp_members WHERE username=:username";
            $bind = array('username' => $username);
            $result2 = $oDb->fetchAll($sSQL, $bind);
            foreach ($result2[0][$table] as $k => $v) {
                $ret['data'][$k] = $v;
            }
        }

        return $ret;
    }

    public function getMemberFields() {
        $table = 'exp_member_fields';
        $oModel = new Model(false, $table, 'ee');
        $oDb = $oModel->getDataSource();
        $sSQL = "SELECT m_field_id AS id, m_field_name AS name FROM exp_member_fields";
        $result = $oDb->fetchAll($sSQL);
        $ret = array();
        foreach ($result as $row) {
            $id = $row[$table]['id'];
            $ret['m_field_id_' . $id] = $row[$table]['name'];
        }
        return $ret;
    }

    /**
     * Authenticate user using uuid 
     * @param Array
     * @return boolean
     */
    public function verify($params) {
        if (!$params['member_id'] || !$params['uuid']) {
            $temp = $params['member_id'] . ':' . $params['uuid'];
            throw new APIException(401, 'authentication_failed', 'Supplied credentials are invalid');
        }
        $rules = array('member_id' => array('required', 'int'), 'uuid' => array('required'));

        $this->validate($params, $rules);
        $member_id = $params['member_id'];
        $uuid = $params['uuid'];
        $table = 'exp_member_data';
        $oModel = new Model(false, $table, 'ee');
        $oDb = $oModel->getDataSource();
        $sSQL = "SELECT member_id FROM $table WHERE m_field_id_128 = :uuid";
        $bind = array(':uuid' => $uuid);
        $result = array();
        $result = $oDb->fetchAll($sSQL, $bind);
        if (!empty($result) && $result[0][$table]['member_id'] === $member_id) {
            return true;
        }
        throw new APIException(401, 'authentication_failed', 'Supplied credentials are invalid');
    }

}
