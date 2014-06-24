<?php

App::uses('APIComponent', 'Controller/Component');
App::uses('ValidatorComponent', 'Controller/Component');
App::uses('SettingComponent', 'Controller/Component');
App::uses('Model', 'Model');
App::import('helper', 'String');
App::import('helper', 'FormValidate');
App::uses('ExpMember', 'Model');

class MemberComponent extends APIComponent {

    public function data($params) {
        $rules = array('member_id' => array('required', 'int'));
        $this->validate($params, $rules);
        $location_id = $params['member_id'];
        $oLocation = new Location();
        $oLocation = $oLocation->find('first', ['conditions'=>['Location.id'=>$location_id]]);
        $aFields = [
            'ap_id'                     => 'network_id',                
            'timezone'                  => 'timezone',
            'store_open'                => 'store_open',
            'store_close'               => 'store_close',
            'lightspeed_id'             => 'pos_id',
            'monday_open'               => 'monday_open',
            'monday_close'              => 'monday_close',
            'tuesday_open'              => 'tuesday_open',
            'tuesday_close'             => 'tuesday_close',
            'wednesday_open'            => 'wednesday_open',
            'wednesday_close'           => 'wednesday_close',
            'thursday_open'             => 'thursday_open',
            'thursday_close'            => 'thursday_close',
            'friday_open'               => 'friday_open',
            'friday_close'              => 'friday_close',
            'saturday_open'             => 'saturday_open',
            'saturday_close'            => 'saturday_close',
            'sunday_open'               => 'sunday_open',
            'sunday_close'              => 'sunday_close',
            'network_provider'          => 'network_provider',
            'register_filter'           => 'register_filter',
            'outlet_filter'             => 'outlet_filter',
            'country'                   => 'country',
            'nightclub_hours'           => 'nightclub_hours',
            'traffic_factor'            => 'traffic_factor',
            'no_rollups'                => 'no_rollups',
            'no_cache'                  => 'no_cache',
            'nightclub_hours_location'  => 'nightclub_hours_location',
            'transactions_while_closed' => 'transactions_while_closed'
        ];
        $tmp = array('data' => array());
        foreach ($aFields as $kOld => $kNew) {
            $tmp['data'][$kOld] = settVal($kNew, $oLocation['Setting']);
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
}
