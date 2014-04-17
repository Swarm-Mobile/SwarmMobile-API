<?php

App::uses('APIController', 'Controller');
App::uses('APIComponent', 'Controller/Component');
App::uses('AppShell', 'Console/Command');
App::uses('Model', 'Model');

class RollupShell extends AppShell {

    private function setEnvironment() {
        $htaccess = file_get_contents(__DIR__.'/../../../.htaccess');
        $pattern = '/.*SetEnv server_location "(.*)"/';
        if (preg_match_all($pattern, $htaccess, $matches)) {
            putenv('server_location=' . $matches[1][0]);
            $_SERVER['server_location'] = $matches[1][0];
        }
    }

    public function main() {
        $this->setEnvironment();
        $member_id = (empty($this->params['member_id'])) ? 'all' : $this->params['member_id'];
        if ($member_id == 'all') {
            $oModel = new Model(false, 'exp_members', 'ee');
            $sSQL = "SELECT member_id FROM exp_members WHERE group_id = 6";
            $aRes = $oModel->query($sSQL);
            $members = array();
            foreach ($aRes as $oRow) {
                $members[] = $oRow["exp_members"]['member_id'];
            }
        } else {
            $members = explode(',', $this->params['member_id']);
        }
        $start_date = (empty($this->params['start_date'])) ? date('Y-m-d', time()) : $this->params['start_date'];
        $end_date = (empty($this->params['end_date'])) ? date('Y-m-d', time() - 7 * 24 * 3600) : $this->params['end_date'];
        $rebuild = (empty($this->params['rebuild'])) ? false : $this->params['rebuild'];
        $rebuild_text = ($rebuild) ? 'YES' : 'NO';
        $this->out("Rebuild : $rebuild_text");
        $this->out("Members to process (ID's):" . implode(' ', $members));
        $this->out("Start Date: $start_date");
        $this->out("End Date  : $end_date");
        $this->out("");
        foreach ($members as $member) {
            $this->out("Processing member :$member");
            $this->out("Start : " . date('H:i:s'));
            if ($rebuild) {
                $this->clean($member);
            }
            $member = trim($member);
            $oAPI = new APIController();
            $result = $oAPI->internalCall('store', 'totals', array(
                'member_id' => $member,
                'start_date' => $start_date,
                'end_date' => $end_date
            ));
            $this->out("End :" . date('H:i:s'));
        }
        $this->out("Done!");
    }

    private function clean($member) {
        $this->out("Cleaning previous rollups");
        $oModel = new Model(false, 'cache', 'mongodb');
        $oModel->deleteAll(array("params.id" => "$member"));
        $this->out("Cleaned");
    }

    public function getOptionParser() {
        $parser = parent::getOptionParser();
        $parser->addOption('member_id', array(
            'short' => 'm',
            'default' => 'all',
            'help' => "Member ID's to rebuild"
        ));
        $parser->addOption('start_date', array(
            'short' => 's',
            'default' => date('Y-m-d', time() - 7 * 24 * 3600),
            'help' => 'Start Date of the rollup'
        ));
        $parser->addOption('end_date', array(
            'short' => 'e',
            'default' => date('Y-m-d'),
            'help' => 'End Date of the rollup'
        ));
        $parser->addOption('rebuild', array(
            'short' => 'r',
            'default' => false,
            'help' => 'Delete the current info and builds it again'
        ));
        return $parser;
    }

}
