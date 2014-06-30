<?php

App::uses('APIComponent', 'Controller/Component');
App::uses('RollupShell', 'Console/Command');

class RollupComponent extends APIComponent {
    public function make($params) {
        $user = $this->api->authenticate($params['access_token']);
        if($user['id'] != 10){
            throw new APIException(401, 'invalid_grant', "Permission denied");
        }
        $rules = array(
            'action' => array('required'),
            'location_id' => array('required', 'int'),
        );
        if ($params['action'] == 'override') {
            $rules['start_date'] = array('required', 'date');
            $rules['end_date'] = array('required', 'date');
        }
        $this->validate($params, $rules);
        $oRollup = new RollupShell();
        $oRollup->params['location_id'] = $params['location_id'];
        $oRollup->params['start_date']  = $params['start_date'];
        $oRollup->params['end_date']    = $params['end_date'];
        $oRollup->params['override']    = $params['action'] == 'override';
        $oRollup->params['rebuild']     = $params['action'] == 'rebuild';
        ob_start();
        $oRollup->main(false);
        $result = ob_get_contents();
        ob_end_clean();
        return array('output'=>  nl2br($result));
    }
}