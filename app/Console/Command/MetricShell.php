<?php

require_once(__DIR__ . '/../../Controller/ApiController.php');
require_once(__DIR__ . '/../../Controller/Component/DBComponent.php');
App::uses('APIComponent', 'Controller/Component');
App::uses('AppShell', 'Console/Command');
App::uses('Model', 'Model');

class MetricShell extends AppShell {

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

    public function getFirstRegisterDate($location) {
        $sSQL = "SELECT value FROM location_setting WHERE setting_id = 6 AND location_id = $location";
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
        throw new Exception('No data on sessions registered for this location.');
    }

    public function main($console = true) {
        $this->console = $console;
        $this->setEnvironment();
        $location_id = (empty($this->params['location_id'])) ? 'all' : $this->params['location_id'];
        $parts = explode('/', $this->params['part']);
        if ($location_id == 'all') {
            $oModel = new Model(false, 'location', 'backstage');
            $sSQL = <<<SQL
SELECT l.id 
FROM location l
INNER JOIN location_setting ls
    ON l.id = ls.location_id
    AND ls.setting_id = 6
    AND value IS NOT NULL
    AND value != ''
    AND value > 0    
SQL;
            $aRes = $oModel->query($sSQL);
            $locations = array();
            foreach ($aRes as $oRow) {
                $locations[] = $oRow['l']['id'];
            }
        } else {
            $locations = explode(',', $this->params['location_id']);
        }
        $tmp = array_chunk($locations, ceil(count($locations) / $parts[1]));
        $locations = $tmp[$parts[0] - 1];
        $rebuild = (empty($this->params['rebuild'])) ? false : $this->params['rebuild'];
        $override = (empty($this->params['override'])) ? false : $this->params['override'];
        $rebuild_text = ($rebuild) ? 'YES' : 'NO';
        $this->output("Full Rebuild                  : $rebuild_text");
        if (!$rebuild) {
            $start_date = (empty($this->params['start_date'])) ? date('Y-m-d', time() + 2 * 24 * 3600) : $this->params['start_date'];
            $end_date = (empty($this->params['end_date'])) ? date('Y-m-d', time() - 7 * 24 * 3600) : $this->params['end_date'];
            $this->output("Start Date               : $start_date");
            $this->output("End Date                 : $end_date");
        }
        $this->output("locations to process (ID's)     : " . implode(' ', $locations));
        $this->output("---------------------------------------------");
        $oAPI = new APIController();
        $oAPI->cache = false;
        $oAPI->rollups = true;
        $index = 0;
        $total = count($locations);
        foreach ($locations as $location) {
            $index++;
            $location = trim($location);
            try {
                $this->output("");
                $this->output("Processing location : $location" . ' (' . $index . '/' . $total . ')');
                $this->output("");
                $this->output("Start             : " . date('H:i:s'));
                if ($rebuild) {
                    $start_date = $this->getFirstRegisterDate($location);
                    $end_date = date('Y-m-d');
                    $this->output("Start Date        : $start_date");
                    $this->output("End Date          : $end_date");
                    $this->output("");
                    $this->output("---------------------------------------------");                    
                    $this->clean($location, $this->params['metric'], $start_date, $end_date);
                } else if ($override) {                    
                    $this->clean($location, $this->params['metric'], $start_date, $end_date);
                }                
                //Prevent empty rollups for customers that don't have sessions
                $this->getFirstRegisterDate($location);
                $this->output("Rebuilding rollups");
                $oAPI->internalCall('location', $this->params['metric'], array(
                    'location_id' => $location,
                    'start_date' => $start_date,
                    'end_date' => $end_date
                ));                
                $this->output("---------------------------------------------");
                $this->output("End               : " . date('H:i:s'));
                $this->output("");
                $handle = fopen(__DIR__ . '/../../tmp/logs/rollup.log', 'a+');
                fwrite($handle, $location . "\n");
                fclose($handle);
            } catch (Exception $e) {
                //Do nothing
                $this->output('Something goes wrong rebuilding');
                $this->output($e->getMessage());
                continue;
            }
        }
        $this->output("Done!");
    }

    private function cleanDay($location, $date, $metric) {
        $oModel = new Model(false, 'totals', 'rollups');
        $oDb = $oModel->getDataSource();
        $sSQL = "DELETE FROM $metric WHERE location_id = :location_id AND date = :date";
        $oDb->query($sSQL, [':location_id'=>$location, ':date'=>$date]);
    }

    private function clean($location, $metric, $start_date = false, $end_date = false) {
        $start_date = (empty($start_date)) ? $this->getFirstRegisterDate($location) : $start_date;
        $end_date = (empty($end_date)) ? date('Y-m-d') : $end_date;
        $end = new DateTime($end_date);
        $date = $start_date;
        do {
            $this->cleanDay($location, $date, $metric);
            $start_date = new DateTime($date);
            date_add($start_date, date_interval_create_from_date_string('1 days'));
            $date = date_format($start_date, 'Y-m-d');
        } while ($start_date <= $end);
    }

    public function getOptionParser() {
        $parser = parent::getOptionParser();
        $parser->addOption('location_id', array(
            'short' => 'm',
            'default' => 'all',
            'help' => "Location ID's to rebuild"
        ));
        $parser->addOption('start_date', array(
            'short' => 's',
            'default' => date('Y-m-d', time() - 3 * 24 * 3600),
            'help' => 'Start Date of the rollup'
        ));
        $parser->addOption('end_date', array(
            'short' => 'e',
            'default' => date('Y-m-d', time() + 2 * 24 * 3600),
            'help' => 'End Date of the rollup'
        ));
        $parser->addOption('override', array(
            'short' => 'o',
            'default' => false,
            'help' => 'Delete the interval info and builds it again'
        ));
        $parser->addOption('metric', array(
            'short' => 'x',
            'default' => false,
            'help' => 'Metric to process'
        ));
        $parser->addOption('rebuild', array(
            'short' => 'r',
            'default' => false,
            'help' => 'Delete all the HISTORICAL rollups info and rebuilds it again'
        ));
        $parser->addOption('part', array(
            'short' => 'p',
            'default' => '1/1',
            'help' => 'Slice of locations that you like to process (1/1 means all 1/2 means the first half, 2/2 the second half...)'
        ));
        return $parser;
    }

}
