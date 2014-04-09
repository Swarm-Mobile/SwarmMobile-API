<?php

App::uses('APIController', 'Controller');
App::uses('APIComponent', 'Controller/Component');
App::uses('AppShell', 'Console/Command');
App::uses('Model', 'Model');

/**
 * Description of DataShell
 *
 * @author gautham
 */
class RollupShell extends AppShell {
    
    public function main() {
		if($this->params['member_id'] == 'all' || empty($this->params['all'])){
			$oModel = new Model(false, 'exp_members', 'ee');
			$sSQL = "SELECT member_id FROM exp_members WHERE group_id = 6";
			$aRes = $oModel->query($sSQL);
			$members = array();
			foreach($aRes as $oRow){
				$members[] = $oRow["exp_members"]['member_id'];
			}
		} else {
			$members = explode(',', $this->params['member_id']);
		}
		$start_date = (empty($this->params['start_date']))?date('Y-m-d', time()-7*24*3600):$this->params['start_date'];
		$end_date = (empty($this->params['end_date']))?date('Y-m-d', time()-7*24*3600):$this->params['end_date'];
		foreach($members as $member){
			$member = trim($member);
			$oAPI = new APIController();
			$oAPI->internalCall('store', 'totals', array(
				'member_id'=>$member,
				'start_date'=>$start_date,
				'end_date'=>$end_date
			));
		}
        $this->out("\nDone!");
    }
    
    public function getOptionParser() {
        $parser = parent::getOptionParser();
        $parser->addOption('member_id', array(
            'short'     => 'm',
            'default'   => 'all',
            'help'      => "Member ID's to rebuild"
        ));
        $parser->addOption('start_date', array(
            'short'     => 'sd',
            'default'   => date('Y-m-d', time() - 7 * 24 * 3600),
            'help'      => 'Start Date of the rebuild'
        ));
        $parser->addOption('end_date', array(
            'short'     => 'sd',
            'default'   => date('Y-m-d'),
            'help'      => 'End Date of the rebuild'
        ));
        return $parser;
    }   
}