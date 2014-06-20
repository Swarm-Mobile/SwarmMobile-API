<?php

App::uses('APIComponent', 'Controller/Component');

class PortalComponent extends APIComponent {

    public function visitorEvent($params) {
        if (!empty($_POST)) {
            $aRows = json_decode($_POST['upload'], true);
            $sSQL = <<<SQL
INSERT INTO visitorevent
    SET device_id = :device_id,
        entered = :entered,
        exited = :exited,
        total_count = :total_count,
        user_id = :user_id,
        ts = :ts,
        ts_creation = NOW(),
        ts_update = NOW()
SQL;
            foreach ($aRows as $oRow) {
                $oModel = new Model(false, 'visitorevent', 'portal');
                $oDb = $oModel->getDataSource();
                $date = new DateTime($oRow['date']);               
                $oDb->query($sSQL, array(
                    ':device_id' => 0,
                    ':entered' => $oRow['entered'],
                    ':exited' => $oRow['exited'],
                    ':total_count' => $oRow['totalCount'],
                    ':user_id' => 0,
                    ':ts' => $date->format('Y-m-d H:i:s'),
                ));
            }
            $file = realpath(__DIR__ . '/../../../../') . '/raw/' . date('Y_m_d_h_i_s_') . uniqid();
            $content = var_export($_POST, true);
            file_put_contents($file, $content);
        }
        return array();
    }

}
