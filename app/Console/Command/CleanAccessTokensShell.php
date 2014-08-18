<?php

require_once(__DIR__ . '/../../Controller/Component/DBComponent.php');
App::uses('AppShell', 'Console/Command');
App::uses('Model', 'Model');

class CleanAccessTokensShell extends AppShell
{

    public function main ()
    {
        $this->setEnvironment();
        $week   = 3600 * 24 * 7;
        $oModel = new Model(false, 'refresh_tokens', 'oauth');
        $oDb    = $oModel->getDataSource();
        $tables = ['access_tokens', 'refresh_tokens', 'auth_codes'];
        foreach ($tables as $table) {
            $sSQL = "DELETE FROM $table WHERE NOW() > FROM_UNIXTIME(expires) + $week";
            $oDb->query($sSQL);
        }
        $this->out("Done!");
    }

}
