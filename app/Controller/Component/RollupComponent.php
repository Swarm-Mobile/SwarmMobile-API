<?php

App::uses('APIComponent', 'Controller/Component');
App::uses('RollupShell', 'Console/Command');

class RollupComponent extends APIComponent {
    public function make($params) {
        $rules = array(
            'action' => array('required'),
            'member_id' => array('required', 'int'),
        );
        if ($params['action'] == 'override') {
            $rules['start_date'] = array('required', 'date');
            $rules['end_date'] = array('required', 'date');
        }
        $this->validate($params, $rules);
        $oRollup = new RollupShell();
        $oRollup->params['member_id']   = $params['member_id'];
        $oRollup->params['start_date']  = $params['start_date'];
        $oRollup->params['end_date']    = $params['end_date'];
        $oRollup->params['override']    = $params['action'] == 'override';
        $oRollup->params['rebuild']     = $params['action'] == 'rebuild';
        ob_start();
        $oRollup->main();
        $result = ob_get_contents();
        ob_end_clean();
        return json_encode(array('output'=>$result));        
    }
}
