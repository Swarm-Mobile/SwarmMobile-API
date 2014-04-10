<?php

App::uses('APIController', 'Controller');
App::uses('APIComponent', 'Controller/Component');
App::uses('AppShell', 'Console/Command');
App::uses('Model', 'Model');

class RollupShell extends AppShell {

    public function main() {
        echo date('H:i:s')."\n";
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
        //$start_date = (empty($this->params['start_date'])) ? date('Y-m-d', time()) : $this->params['start_date'];
        //$end_date = (empty($this->params['end_date'])) ? date('Y-m-d', time() - 7 * 24 * 3600) : $this->params['end_date'];
        $start_date = '2014-03-01';
        $end_date = '2014-03-31';
        foreach ($members as $member) {
            $member = trim($member);
            $oAPI = new APIController();
            $result = $oAPI->internalCall('store', 'walkbys', array(
                'member_id' => $member,
                'start_date' => $start_date,
                'end_date' => $end_date
            ));
        }
        echo date('H:i:s')."\n";
        $this->out("\nDone!");
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
            'help' => 'Start Date of the rebuild'
        ));
        $parser->addOption('end_date', array(
            'short' => 'sd',
            'default' => date('Y-m-d'),
            'help' => 'End Date of the rebuild'
        ));
        return $parser;
    }

}
