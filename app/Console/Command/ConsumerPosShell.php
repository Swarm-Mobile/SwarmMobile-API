<?php

require_once(__DIR__ . '/../../Controller/Component/DBComponent.php');
App::uses('AppShell', 'Console/Command');
App::uses('Model', 'Model');

class ConsumerPosShell extends AppShell {

    private $console = true;

    private function setEnvironment() {
        $htaccess = file_get_contents(__DIR__ . '/../../../.htaccess');
        $pattern = '/.*SetEnv server_location "(.*)"/';
        if (preg_match_all($pattern, $htaccess, $matches)) {
            putenv('server_location=' . $matches[1][0]);
            $_SERVER['server_location'] = $matches[1][0];
        }
    }
    
    public function main($console = true) {
        $this->console = $console;
        $this->setEnvironment();
        
        $this->output("Done!");
    }

    public function getOptionParser() {
        $parser = parent::getOptionParser();
//        $parser->addOption('location_id', array(
//            'short' => 'm',
//            'default' => 'all',
//            'help' => "Location ID's to rebuild"
//        ));
        return $parser;
    }

}
