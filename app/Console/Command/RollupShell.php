<?php

require_once(__DIR__ . '/../../Controller/ApiController.php');
App::uses('APIComponent', 'Controller/Component');
App::uses('AppShell', 'Console/Command');
App::uses('Model', 'Model');

class RollupShell extends AppShell {

    private function setEnvironment() {
        $htaccess = file_get_contents(__DIR__ . '/../../../.htaccess');
        $pattern = '/.*SetEnv server_location "(.*)"/';
        if (preg_match_all($pattern, $htaccess, $matches)) {
            putenv('server_location=' . $matches[1][0]);
            $_SERVER['server_location'] = $matches[1][0];
        }
    }

    public function getFirstRegisterDate($member) {
        $sSQL = "SELECT m_field_id_20 FROM exp_member_data WHERE member_id = $member";
        $oModel = new Model(false, 'exp_member_data', 'ee');
        $oDb = $oModel->getDataSource();
        $result = $oDb->query($sSQL);
        $ap_id = $result[0]['exp_member_data']['m_field_id_20'];
        $aTables = array('sessions_archive', 'sessions');
        foreach ($aTables as $table) {
            $sSQL = <<<SQL
SELECT DATE(time_login) as first_date
FROM $table 
WHERE network_id = $ap_id  
ORDER BY time_login ASC  
LIMIT 1
SQL;
            $oModel = new Model(false, 'sessions', 'swarmdata');
            $oDb = $oModel->getDataSource();
            $result = $oDb->query($sSQL);
            if (!empty($result)) {
                return $result[0][0]['first_date'];
            }
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
        $rebuild = (empty($this->params['rebuild'])) ? false : $this->params['rebuild'];
        $override = (empty($this->params['override'])) ? false : $this->params['override'];
        $rebuild_text = ($rebuild) ? 'YES' : 'NO';
        $this->out("Rebuild                  : $rebuild_text");
        if (!$rebuild) {
            $start_date = (empty($this->params['start_date'])) ? date('Y-m-d', time()) : $this->params['start_date'];
            $end_date = (empty($this->params['end_date'])) ? date('Y-m-d', time() - 7 * 24 * 3600) : $this->params['end_date'];
            $this->out("Start Date               : $start_date");
            $this->out("End Date                 : $end_date");
        }
        $this->out("Members to process (ID's): " . implode(' ', $members));
        $this->out("");
        foreach ($members as $member) {
            $this->out("Processing member : $member");
            $this->out("");
            $this->out("Start             : " . date('H:i:s'));
            if ($rebuild) {
                $start_date = $this->getFirstRegisterDate($member);
                $end_date = date('Y-m-d');
                $this->out("Start Date        : $start_date");
                $this->out("End Date          : $end_date");
                $this->clean($member);
            } else if ($override) {
                $this->clean($member, $start_date, $end_date);
            }
            $member = trim($member);
            $oAPI = new APIController();
            $oAPI->cache = true;
            $oAPI->rollups = true;
            $result = $oAPI->internalCall('store', 'totals', array(
                'member_id' => $member,
                'start_date' => $start_date,
                'end_date' => $end_date
            ));
            $this->out('Requests cached   : ' . $this->mongoResults($member));
            $this->out("");
            $this->out("End               : " . date('H:i:s'));
            $this->out("");
        }
        $this->out("Done!");
    }

    private function mongoResults($member, $start_date = false) {
        $oModel = new Model(false, 'cache', 'mongodb');
        if($start_date){            
            $aRes = $oModel->find('all', array(
                'conditions'=>array(
                    "params.member_id" => "$member",
                    "params.start_date" => "$start_date",
                )
            ));            
        } else {
            $aRes = $oModel->find('all', array(
                'conditions' =>array(
                    "params.member_id" => "$member"
                )
            ));            
        }
        return count($aRes);
    }

    private function cleanAll($member) {
        $this->out("");
        $this->out("Cleaning previous rollups");
        $this->out('Results before: ' . $this->mongoResults($member));
        $oModel = new Model(false, 'cache', 'mongodb');
        $oModel->deleteAll(array("params.member_id" => "$member"));
        $this->out('Results after: ' . $this->mongoResults($member));
        $this->out("Cleaned");
        $this->out("");
    }

    private function cleanDay($member, $date) {
        $this->out("");
        $this->out("Cleaning rollups for date: $date");        
        $this->out('Results before: ' . $this->mongoResults($member, $date));
        $oModel = new Model(false, 'cache', 'mongodb');
        $oModel->deleteAll(array(
            "params.member_id" => "$member",
            "params.start_date" => "$date"
        ));        
        $this->out('Results after: ' . $this->mongoResults($member, $date));
    }

    private function clean($member, $start_date = false, $end_date = false) {
        if ($start_date === false && $end_date === false) {
            $this->cleanAll($member);
        } else {
            $this->out("");
            $this->out('Results before: ' . $this->mongoResults($member));
            $start_date = (empty($start_date)) ? $this->getFirstRegisterDate($member) : $start_date;
            $end_date = (empty($end_date)) ? date('Y-m-d') : $end_date;
            $end = new DateTime($end_date);
            $date = $start_date;            
            do {
                $this->cleanDay($member, $date);
                $start_date = new DateTime($date);
                date_add($start_date, date_interval_create_from_date_string('1 days'));
                $date = date_format($start_date, 'Y-m-d');
            } while ($start_date <= $end);            
            $this->out('Results after: ' . $this->mongoResults($member));
            $this->out("");
            $this->out("Cleaned");
            $this->out("");
        }
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
            'default' => date('Y-m-d', time() - 3 * 24 * 3600),
            'help' => 'Start Date of the rollup'
        ));
        $parser->addOption('end_date', array(
            'short' => 'e',
            'default' => date('Y-m-d'),
            'help' => 'End Date of the rollup'
        ));
        $parser->addOption('override', array(
            'short' => 'o',
            'default' => false,
            'help' => 'Delete the interval info and builds it again'
        ));
        $parser->addOption('rebuild', array(
            'short' => 'r',
            'default' => false,
            'help' => 'Delete all the HISTORICAL mongodb info and rebuilds it again'
        ));
        return $parser;
    }

}
