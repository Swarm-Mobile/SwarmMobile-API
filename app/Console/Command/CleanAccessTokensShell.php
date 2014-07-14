<?php

require_once(__DIR__ . '/../../Controller/Component/DBComponent.php');
App::uses('AppShell', 'Console/Command');
App::uses('Model', 'Model');

class CleanAccessTokensShell extends AppShell {

    public function main() {
        $week = 3600 * 24 * 7;
        $sSQL = "DELETE FROM access_tokens WHERE NOW() > FROM_UNIXTIME(expires) + $week";
        $oModel = new Model(false, 'refresh_tokens', 'oauth');
        $oDb = $oModel->getDataSource();
        $oDb->query($sSQL);
        $this->out("Done!");
    }
    
}
