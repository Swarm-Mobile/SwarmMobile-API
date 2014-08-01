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
            foreach ($upload as $oRow) {
                $oModel = new Model(false, 'visitorEvent', 'portal');
                $oDb = $oModel->getDataSource();                
                $oDb->query($sSQL, array(
                    ':device_id' => $oRow['serialNumber'],
                    ':entered' => $oRow['entered'],
                    ':location_id' => $oRow['locationID'],
                    ':exited' => $oRow['exited'],
                    ':total_count' => $oRow['totalCount'],
                    ':user_id' => $oRow['userID'],
                    ':ts' => $oRow['date'],
                ));
            }
        }
        return array();
    }

    public function setStatus($params){
        return array();
    }
}
