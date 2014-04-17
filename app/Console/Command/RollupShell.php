<?php

App::uses('APIController', 'Controller');
App::uses('APIComponent', 'Controller/Component');
App::uses('AppShell', 'Console/Command');
App::uses('Model', 'Model');

class RollupShell extends AppShell {

    public function main() {
        if ($this->params['member_id'] == 'all' || empty($this->params['all'])) {
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
        $rebuild_text = ($rebuild)?'YES':'NO';
        $this->out("\nRebuild : $rebuild_text");
        $this->out("\nMembers to process :" . implode(' ', $members));
        $this->out("\nStart Date: $start_date");
        $this->out("\nEnd Date  : $end_date");
        $this->out("\n");
        foreach ($members as $member) {
            $this->out("\nProcessing member :$member");
            $this->out("\nStart : " . date('H:i:s'));
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
            $this->out("\nEnd :" . date('H:i:s'));
        }
        $this->out("\nDone!");
    }

    private function clean($member) {
        $this->out("\nCleaning previous rollups");
        $oModel = new Model(false, 'cache', 'mongodb');
        $oModel->deleteAll(array("params.id" => "$member"));
        $this->out("\nCleaned");
    }

    public function getOptionParser() {
        $parser = parent::getOptionParser();
        $parser->addOption('member_id', array(
            'short' => 'm',
            'default' => 'all',
            'help' => "Member ID's to rebuild"
        ));
        $parser->addOption('start_date', array(
            'short' => 'sd',
            'default' => date('Y-m-d', time() - 7 * 24 * 3600),
            'help' => 'Start Date of the rollup'
        ));
        $parser->addOption('end_date', array(
            'short' => 'sd',
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
