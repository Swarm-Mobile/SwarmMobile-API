<?php

require_once(__DIR__ . '/../../Controller/ApiController.php');
require_once(__DIR__ . '/../../Controller/Component/DBComponent.php');
App::uses('APIComponent', 'Controller/Component');
App::uses('AppShell', 'Console/Command');
App::uses('Model', 'Model');
App::uses('CakeEmail', 'Network/Email');

class RollupShell extends AppShell {

    private $console = true;

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
        $hash = uniqid();
        $this->console = $console;
        $this->setEnvironment();
        $location_id = (empty($this->params['location_id'])) ? 'all' : $this->params['location_id'];
        $parts = explode('/', $this->params['part']);
        $minute = date('i');
        $minute = $minute % 30;
        $parts[0] = $parts[0] == 'start' ? $minute + 1 : $parts[0];
        $parts[0] = $parts[0] == 'end' ? 60 - $minute : $parts[0];
        $log = fopen(__DIR__ . '/../../tmp/logs/rollup.log', 'a+');
        fwrite($log, 'INI:' . date('Y-m-d H:i:s') . ' ' . $parts[0] . '/' . $parts[1] . ' HASH:' . $hash . "\n");
        fclose($log);

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
UNION DISTINCT 
SELECT l.id 
FROM location l
INNER JOIN device d
    ON l.id = d.location_id
WHERE d.devicetype_id=2  
SQL;
            $aRes = $oModel->query($sSQL);
            $locations = array();
            foreach ($aRes as $oRow) {
                $locations[] = $oRow['0']['id'];
            }
        } else {
            $locations = explode(',', $this->params['location_id']);
        }
        $tmp = array_chunk($locations, ceil(count($locations) / $parts[1]));
        $locations = $tmp[$parts[0] - 1];
        if(empty($locations)){
            $log = fopen(__DIR__ . '/../../tmp/logs/rollup.log', 'a+');
            fwrite($log, 'END:' . date('Y-m-d H:i:s') . ' ' . $parts[0] . '/' . $parts[1] . ' HASH:' . $hash . "\n");
            fclose($log);
            exit();
        }
        $rebuild = (empty($this->params['rebuild'])) ? false : $this->params['rebuild'];
        $override = (empty($this->params['override'])) ? false : $this->params['override'];
        $rebuild_text = ($rebuild) ? 'YES' : 'NO';
        $this->output("Full Rebuild                  : $rebuild_text");
        $this->output("Section                       : {$parts[0]} of {$parts[1]}");
        if (!$rebuild) {
            $start_date = (empty($this->params['start_date'])) ? date('Y-m-d', time() + 2 * 24 * 3600) : $this->params['start_date'];
            $end_date = (empty($this->params['end_date'])) ? date('Y-m-d', time() - 7 * 24 * 3600) : $this->params['end_date'];
            $this->output("Start Date               : $start_date");
            $this->output("End Date                 : $end_date");
        }
        $this->output("Locations to process (ID's)     : " . implode(' ', $locations));
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
                    $this->clean($location, $start_date, $end_date);
                } else if ($override) {
                    $this->clean($location, $start_date, $end_date);
                }
                //Prevent empty rollups for customers that don't have sessions
                $this->getFirstRegisterDate($location);
                $this->output("Rebuilding rollups");
                $oAPI->internalCall('location', 'totals', array(
                    'location_id' => $location,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'rollup' => true
                ));
                $this->output("---------------------------------------------");
                $this->output("End               : " . date('H:i:s'));
                $this->output("");
            } catch (Exception $e) {
                //Do nothing
                $this->output('Something goes wrong rebuilding');
                $this->output($e->getMessage());
                if ($e->getMessage() != 'No data on sessions registered for this location.') {
                    $this->output('Sending email to dev@swarm-mobile.com');
                    $Email = new CakeEmail();
                    $Email->from(array('info@swarm-mobile.com' => 'Info'));
                    $Email->to('dev@swarm-mobile.com');
                    $Email->subject('Rollup Issue: Location #' . $location_id);
                    $Email->send('The script throw: ' . $e->getMessage());
                    $this->output('Email sended.');
                }
                continue;
            }
        }
        $log = fopen(__DIR__ . '/../../tmp/logs/rollup.log', 'a+');
        fwrite($log, 'END:' . date('Y-m-d H:i:s') . ' ' . $parts[0] . '/' . $parts[1] . ' HASH:' . $hash . "\n");
        fclose($log);
    }

    private function cleanDay($location, $date) {
        $oModel = new Model(false, 'walkbys', 'rollups');
        $oDb = $oModel->getDataSource();
        $metrics = [
            'avgTicket',
            'devices',
            'dwell',
            'itemsPerTransaction',
            'presenceConversionRate',
            'presenceTraffic',
            'returning',
            'revenue',
            'timeInShop',
            'totalItems',
            'totals',
            'traffic',
            'transactions',
            'walkbys',
            'windowConversion',            
        ];
        foreach ($metrics as $metric) {
            $sSQL = "DELETE FROM $metric WHERE location_id = :location_id AND date = :date";
            $oDb->query($sSQL, [':location_id' => $location, ':date' => $date]);
        }
    }

    private function clean($location, $start_date = false, $end_date = false) {
        $start_date = (empty($start_date)) ? $this->getFirstRegisterDate($location) : $start_date;
        $end_date = (empty($end_date)) ? date('Y-m-d') : $end_date;
        $end = new DateTime($end_date);
        $date = $start_date;
        do {
            $this->cleanDay($location, $date);
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
