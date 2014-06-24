<?php

require_once(__DIR__ . '/../../Controller/ApiController.php');
require_once(__DIR__ . '/../../Controller/Component/DBComponent.php');
App::uses('APIComponent', 'Controller/Component');
App::uses('AppShell', 'Console/Command');
App::uses('Model', 'Model');
App::uses('CakeEmail', 'Network/Email');

class RollupShell extends AppShell {

    private $console = true;

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

    public function getFirstRegisterDate($member) {
        $sSQL = "SELECT value FROM location_setting WHERE setting_id = 6 AND location_id = $member";
        $oModel = new Model(false, 'location_setting', 'backstage');
        $oDb = $oModel->getDataSource();
        $result = $oDb->query($sSQL);
        $ap_id = $result[0]['location_setting']['value'];
        $ap_id = (empty($ap_id)) ? 0 : $ap_id;
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
            if (empty($result)) {
                continue;
            }
            $first_date = $result[0][0]['first_date'];
            $first_date = new DateTime($first_date);
            $swarm_born = new DateTime('2013-01-01');
            return ($first_date < $swarm_born) ? '2013-01-01' : $result[0][0]['first_date'];
        }
        throw new Exception('No data on sessions registered for this store.');
    }

    public function main($console = true) {
        $this->console = $console;
        $this->setEnvironment();
        $member_id = (empty($this->params['member_id'])) ? 'all' : $this->params['member_id'];
        $parts = explode('/', $this->params['part']);
        if ($member_id == 'all') {
            $oModel = new Model(false, 'location', 'backstage');
            $sSQL = <<<SQL
SELECT l.location_id 
FROM location l
INNER JOIN location_setting ls
    ON l.location_id = ls.location_id
    AND ls.setting_id = 6
    AND value IS NOT NULL
    AND value != ''
    AND value > 0    
SQL;
            $aRes = $oModel->query($sSQL);
            $members = array();
            foreach ($aRes as $oRow) {
                $members[] = $oRow['l']['location_id'];
            }
        } else {
            $members = explode(',', $this->params['member_id']);
        }
        $tmp = array_chunk($members, ceil(count($members) / $parts[1]));
        $members = $tmp[$parts[0] - 1];
        $rebuild = (empty($this->params['rebuild'])) ? false : $this->params['rebuild'];
        $override = (empty($this->params['override'])) ? false : $this->params['override'];
        $rebuild_text = ($rebuild) ? 'YES' : 'NO';
        $this->output("Full Rebuild                  : $rebuild_text");
        if (!$rebuild) {
            $start_date = (empty($this->params['start_date'])) ? date('Y-m-d', time()) : $this->params['start_date'];
            $end_date = (empty($this->params['end_date'])) ? date('Y-m-d', time() - 7 * 24 * 3600) : $this->params['end_date'];
            $this->output("Start Date               : $start_date");
            $this->output("End Date                 : $end_date");
        }
        $this->output("Members to process (ID's)     : " . implode(' ', $members));
        $this->output("---------------------------------------------");
        $oAPI = new APIController();
        $oAPI->cache = false;
        $oAPI->rollups = true;
        $index = 0;
        $total = count($members);
        foreach ($members as $member) {
            $index++;
            $member = trim($member);
            try {
                $this->output("");
                $this->output("Processing member : $member".' ('.$index.'/'.$total.')');
                $this->output("");
                $this->output("Start             : " . date('H:i:s'));
                if ($rebuild) {
                    $start_date = $this->getFirstRegisterDate($member);
                    $end_date = date('Y-m-d');
                    $this->output("Start Date        : $start_date");
                    $this->output("End Date          : $end_date");
                    $this->output("");
                    $this->output("---------------------------------------------");
                    $this->output('Elements cached before clean: ' . $this->mongoResults($member));
                    $this->clean($member, $start_date, $end_date);
                } else if ($override) {
                    $this->output('Elements cached before clean: ' . $this->mongoResults($member));
                    $this->clean($member, $start_date, $end_date);
                }
                $this->output('Elements cached before rebuild: ' . $this->mongoResults($member));
                //Prevent empty rollups for customers that don't have sessions
                $this->getFirstRegisterDate($member);
                $this->output("Rebuilding rollups");
                $end = new DateTime($end_date);
                do {
                    //$this->output('Member #'.$member.' ('.$index.'/'.$total.') Processing: ' . $start_date);
                    $end_date = $start_date;
                    $oAPI->internalCall('store', 'totals', array(
                        'member_id' => $member,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'rollup' => true
                    ));
                    $start = new DateTime($start_date);
                    date_add($start, date_interval_create_from_date_string('1 days'));
                    $start_date = date_format($start, 'Y-m-d');
                } while ($start <= $end);
                $this->output('Elements cached after rebuild: ' . $this->mongoResults($member));
                $this->output("---------------------------------------------");
                $this->output("End               : " . date('H:i:s'));
                $this->output("");
                $handle = fopen(__DIR__ . '/../../tmp/logs/rollup.log', 'a+');
                fwrite($handle, $member . "\n");
                fclose($handle);
            } catch (Exception $e) {
                //Do nothing
                $this->output('Something goes wrong rebuilding');
                $this->output($e->getMessage());
                if ($e->getMessage() != 'No data on sessions registered for this store.') {
                    $this->output('Sending email to dev@swarm-mobile.com');
                    $Email = new CakeEmail();
                    $Email->from(array('info@swarm-mobile.com' => 'Info'));
                    $Email->to('dev@swarm-mobile.com');
                    $Email->subject('Rollup Issue: Member #' . $member_id);
                    $Email->send('The script throw: ' . $e->getMessage());
                    $this->output('Email sended.');
                }
                continue;
            }
        }
        $this->output("Done!");
    }

    private function mongoResults($member, $start_date = false) {
        $oModel = new Model(false, 'cache', 'mongodb');
        if ($start_date) {
            $aRes = $oModel->find('all', array(
                'conditions' => array(
                    "params.member_id" => "$member",
                    "params.start_date" => "$start_date",
                )
            ));
        } else {
            $aRes = $oModel->find('all', array(
                'conditions' => array(
                    "params.member_id" => "$member"
                )
            ));
        }
        return count($aRes);
    }

    private function cleanDay($member, $date) {
        $oModel = new Model(false, 'cache', 'mongodb');
        $oModel->deleteAll(array(
            "params.member_id" => "$member",
            "params.start_date" => "$date"
        ));
    }

    private function clean($member, $start_date = false, $end_date = false) {
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
            'default' => date('Y-m-d', time() + 1 * 24 * 3600),
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
        $parser->addOption('part', array(
            'short' => 'p',
            'default' => '1/1',
            'help' => 'Slice of members that you like to process (1/1 means all 1/2 means the first half, 2/2 the second half...)'
        ));
        return $parser;
    }

}
