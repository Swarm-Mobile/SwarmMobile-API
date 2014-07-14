<?php

require_once(__DIR__ . '/../../Controller/Component/DBComponent.php');
App::uses('AppShell', 'Console/Command');
App::uses('Model', 'Model');

class CleanAccessTokensShell extends AppShell {

    private function setEnvironment($env = false) {
        if (!$env) {
            $htaccess = file_get_contents(__DIR__ . '/../../../.htaccess');
            $pattern = '/.*SetEnv server_location "(.*)"/';
            if (preg_match_all($pattern, $htaccess, $matches)) {
                putenv('server_location=' . $matches[1][0]);
                $_SERVER['server_location'] = $matches[1][0];
            }
        } else {
            $_SERVER['server_location'] = $env;
        }
    }

    public function main() {
        $this->setEnvironment();
        $week = 3600 * 24 * 7;
        $oModel = new Model(false, 'refresh_tokens', 'oauth');
        $oDb = $oModel->getDataSource();
        $tables = ['access_tokens', 'refresh_tokens', 'auth_codes'];
        foreach($tables as $table){
            $sSQL = "DELETE FROM $table WHERE NOW() > FROM_UNIXTIME(expires) + $week";            
            $oDb->query($sSQL);
        }
        $this->out("Done!");
    }

}
