<?php

App::uses('AppShell', 'Console/Command');

class CleanAccessTokensShell extends AppShell {

    public function main() {
        $sSQL = "DELETE FROM access_tokens WHERE NOW() > FROM_UNIXTIME(expires)";
        $oModel = new Model(false, 'refresh_tokens', 'oauth');
        $oDb = $oModel->getDataSource();
        $oDb->query($sSQL);
        $this->out("Done!");
    }
    
}
