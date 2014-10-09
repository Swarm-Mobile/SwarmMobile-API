<?php

App::uses('APIComponent', 'Controller/Component');

class PortalComponent extends APIComponent {
    
    public $post_actions    = ['visitorEvent'];
    public $put_actions     = [];
    public $delete_actions  = [];

    public function visitorEvent($params) {
        if (!empty($_POST['upload'])) {            
            $sSQL = <<<SQL
INSERT INTO visitorEvent
    SET device_id = :device_id,
        entered = :entered,
        exited = :exited,
        total_count = :total_count,
        user_id = :user_id,
        location_id = :location_id,
        ts = :ts,
        ts_creation = NOW(),
        ts_update = NOW()
SQL;
            $upload = (is_array($_POST['upload']))?$_POST['upload']:json_decode($_POST['upload'],true);
            if(!empty($upload) && is_array($upload)) {
                foreach ($upload as $oRow) {
                    $oModel = new Model(false, 'visitorEvent', 'portal');
                    $oDb = $oModel->getDataSource();
                                  
                    $oDb->query($sSQL, array(
                        ':device_id' => ($oRow['serialNumber']) ? $oRow['serialNumber'] : NULL,
                        ':entered' => ($oRow['entered']) ? $oRow['entered'] : NULL,
                        ':location_id' => ($oRow['locationID']) ? $oRow['locationID'] : NULL,
                        ':exited' => ($oRow['exited']) ? $oRow['exited'] : NULL,
                        ':total_count' => ($oRow['totalCount']) ? $oRow['totalCount'] : NULL,
                        ':user_id' => ($oRow['userID']) ? $oRow['userID'] : NULL,
                        ':ts' => ($oRow['date']) ? $oRow['date'] : '0000-00-00 00:00:00',
                    ));
                }
            }
        }
        return array();
    }

    public function setStatus($params){
        return array();
    }
}
