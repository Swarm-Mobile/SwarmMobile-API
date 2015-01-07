<?php

App::uses('AppShell', 'Console/Command');
App::uses('MetricFormatComponent', 'Controller/Component');
App::uses('Model', 'Model');
App::uses('LocationSetting', 'Model/Location');
App::uses('Device', 'Model/Device');
App::uses('DeviceType', 'Model/Device');
App::uses('CakeEmail', 'Network/Email');

class RollupShell extends AppShell
{

    private $console = true;
    public $metrics  = [
        'walkbys',
        'transactions',
        'portalTraffic',
        'presenceReturningByHour',
        'presenceReturningByDate',
        'presenceTrafficByHour',
        'presenceTrafficByDate',
        'revenue',
        'totalItems',
        'traffic',
        'devices',
        'timeInShop',
        'wifiConnections',
        'emailsCaptured',
    ];

    private function getFirstRegisterDate ($locationId)
    {
        $locationSetting = new LocationSetting();
        $locationSetting->create(['location_id' => $locationId], true);
        $networkId       = $locationSetting->getSettingValue(LocationSetting::NETWORK_ID);
        $swarmBorn       = new DateTime('2013-01-01');
        if (!empty($networkId)) {
            $tables = ['sessions_archive', 'sessions'];
            $model  = new Model(false, 'sessions', 'swarmdata');
            $db     = $model->getDataSource();
            foreach ($tables as $table) {
                $query  = [
                    'fields'     => ['DATE(time_login) as first_date'],
                    'table'      => $db->fullTableName($table),
                    'alias'      => 'Session',
                    'conditions' => ['network_id' => $networkId],
                    'limit'      => 1,
                    'order'      => ['time_login ASC']
                ];
                $sql    = $db->buildStatement($query, $model);
                $result = $db->query($sql);
                if (empty($result)) {
                    continue;
                }
                $firstDate = $result[0][0]['first_date'];
                $firstDate = new DateTime($firstDate);
                return ($firstDate < $swarmBorn) ? '2013-01-01' : $result[0][0]['first_date'];
            }
        }
        $model  = new Model(false, 'visitorEvent', 'portal');
        $db     = $model->getDataSource();
        $query  = [
            'fields'     => ['DATE(ts) as first_date'],
            'table'      => $db->fullTableName($table),
            'alias'      => 'VisitorEvent',
            'conditions' => ['location_id' => $locationId],
            'limit'      => 1,
            'order'      => ['ts ASC']
        ];
        $sql    = $db->buildStatement($query, $model);
        $result = $db->query($sql);
        if (empty($result)) {
            throw new Swarm\ApplicationErrorException(SwarmErrorCodes::setError('No data on sessions registered for this location.'));
        }
        $firstDate = $result[0][0]['first_date'];
        $firstDate = new DateTime($firstDate);
        return ($firstDate < $swarmBorn) ? '2013-01-01' : $result[0][0]['first_date'];
    }

    private function getLocationList ($locationId)
    {
        if ($locationId == 'all') {
            $setting               = new LocationSetting();
            $setting->displayField = 'location_id';
            $presenceLocations     = $setting->find('list', ['conditions' => [
                    'setting_id' => 6,
                    'value >'    => 0,
                    'value !='   => '',
                    'value IS NOT NULL'
            ]]);
            $device                = new Device();
            $device->displayField  = 'location_id';
            $portalLocations       = $device->find('list', ['conditions' => [
                    'location_id >'  => 0,
                    'location_id IS NOT NULL',
                    'location_id !=' => '',
                    'devicetype_id'  => DeviceType::$PORTAL
            ]]);
            $locations             = array_merge(array_values($presenceLocations), array_values($portalLocations));
            $locations             = array_unique($locations);
        }
        else {
            $locations = explode(',', $this->params['location_id']);
        }
        return $locations;
    }

    private function rollup ($locationId, $startDate, $endDate, $filterMetric)
    {
        $date = new DateTime($startDate);
        $end  = new DateTime($endDate);
        do {
            $currentEndDate     = clone($date);
            $currentEndDate     = min([$currentEndDate, $end]);
            $startDateFormatted = $date->format('Y-m-d');
            $endDateFormatted   = $currentEndDate->format('Y-m-d');
            foreach ($this->metrics as $metric) {
                if ($filterMetric == 'all' || $metric == $filterMetric) {
                    $modelName = ucfirst($metric);
                    App::uses($modelName, 'Model/Metrics');
                    $model     = new $modelName();
                    $model->create([
                        'location_id' => $locationId,
                        'start_date'  => $startDateFormatted,
                        'end_date'    => $endDateFormatted], true
                    );
                    $this->output(
                            date('H:i:s') .
                            ' L:' . $locationId .
                            ' SD:' . $startDateFormatted .
                            ' ED:' . $endDateFormatted .
                            ' M: ' . $metric
                            , false);
                    try {
                        $model->storeInCache($model->getFromRaw());
                    }
                    catch (Swarm\ApplicationErrorException $e) {
                        if ($e->getCode() == SwarmErrorCodes::LOCATION_WITHOUT_NETWORK) {
                            continue;
                        }
                    }
                }
            }
            date_add($currentEndDate, date_interval_create_from_date_string('+1 days'));
            $date = $currentEndDate;
        }
        while ($date <= $end);
        $this->output('');
    }

    private function notifyByEmail (Exception $e, $locationId)
    {
        $this->output('Something goes wrong rebuilding');
        $this->output($e->getMessage());
        if ($e->getMessage() != 'No data on sessions registered for this location.') {
            $this->output('Sending email to dev@swarm-mobile.com');
            $Email = new CakeEmail();
            $Email->from(['info@swarm-mobile.com' => 'Info']);
            $Email->to('dev@swarm-mobile.com');
            $Email->subject('Rollup Issue: Location #' . $locationId);
            $Email->send('The script throw: ' . $e->getMessage());
            $this->output('Email sended.');
        }
    }

    private function processLocations ($locations, $parts)
    {
        $rebuild      = (empty($this->params['rebuild'])) ? false : $this->params['rebuild'];
        $filterMetric = (empty($this->params['filter_metric'])) ? 'all' : $this->params['filter_metric'];
        $rebuild_text = ($rebuild) ? 'YES' : 'NO';
        $this->output("Full Rebuild                  : $rebuild_text");
        $this->output("Section                       : {$parts[0]} of {$parts[1]}");
        $this->output("Locations to process (ID's)     : " . implode(' ', $locations));
        $this->output("---------------------------------------------");
        $index        = 0;
        $total        = count($locations);
        foreach ($locations as $locationId) {
            $startDate  = (empty($this->params['start_date'])) ? date('Y-m-d', time() - 7 * 24 * 3600) : $this->params['start_date'];
            $endDate    = (empty($this->params['end_date'])) ? date('Y-m-d', time() + 2 * 24 * 3600) : $this->params['end_date'];
            $index++;
            $locationId = trim($locationId);
            try {
                $this->output("\nProcessing location : $locationId" . ' (' . $index . '/' . $total . ')' . "\n");
                $this->output("Start             : " . date('H:i:s'));
                $firstDate = $this->getFirstRegisterDate($locationId);
                if ($rebuild) {
                    $startDate = $firstDate;
                    $endDate   = date('Y-m-d');
                }
                $this->output("Start Date        : $startDate");
                $this->output("End Date          : $endDate\n");
                $this->output("---------------------------------------------");
                $this->output("Rebuilding rollups");
                $this->rollup($locationId, $startDate, $endDate, $filterMetric);
                $this->output("---------------------------------------------");
                $this->output("End               : " . date('H:i:s') . "\n");
            }
            catch (Exception $e) {
                $this->notifyByEmail($e, $locationId);
                continue;
            }
        }
    }

    private function logMessage ($message)
    {
        $log = fopen(__DIR__ . '/../../tmp/logs/rollup.log', 'a+');
        fwrite($log, $message);
        fclose($log);
    }

    public function main ($console = true)
    {
        $hash          = uniqid();
        $this->console = $console;
        $this->setEnvironment();
        $locationId    = (empty($this->params['location_id'])) ? 'all' : $this->params['location_id'];

        $part     = (empty($this->params['part'])) ? '1/1' : $this->params['part'];
        $parts    = explode('/', $part);
        $minute   = date('i') % 30;
        $parts[0] = $parts[0] == 'start' ? $minute + 1 : $parts[0];
        $parts[0] = $parts[0] == 'end' ? 60 - $minute : $parts[0];

        $this->logMessage('INI:' . date('Y-m-d H:i:s') . ' ' . $parts[0] . '/' . $parts[1] . ' HASH:' . $hash . "\n");
        $locations = $this->getLocationList($locationId);
        $tmp       = array_chunk($locations, ceil(count($locations) / $parts[1]));
        $locations = $tmp[$parts[0] - 1];
        $this->processLocations($locations, $parts);
        $this->logMessage('END:' . date('Y-m-d H:i:s') . ' ' . $parts[0] . '/' . $parts[1] . ' HASH:' . $hash . "\n");
    }

    public function getOptionParser ()
    {
        $parser = parent::getOptionParser();
        $parser->addOption('location_id', [
            'short'   => 'l',
            'default' => 'all',
            'help'    => "Location ID's to rebuild"
        ]);
        $parser->addOption('filter_metric', [
            'short'   => 'm',
            'default' => 'all',
            'help'    => "Filter which metric you want to process: \n" . implode("\n", $this->metrics)
        ]);
        $parser->addOption('start_date', [
            'short'   => 's',
            'default' => date('Y-m-d', time() - 3 * 24 * 3600),
            'help'    => 'Start Date of the rollup'
        ]);
        $parser->addOption('end_date', [
            'short'   => 'e',
            'default' => date('Y-m-d', time() + 2 * 24 * 3600),
            'help'    => 'End Date of the rollup'
        ]);
        $parser->addOption('rebuild', [
            'short'   => 'r',
            'default' => false,
            'help'    => 'Delete all the HISTORICAL rollups info and rebuilds it again'
        ]);
        $parser->addOption('part', [
            'short'   => 'p',
            'default' => '1/1',
            'help'    => 'Slice of locations that you like to process (1/1 means all 1/2 means the first half, 2/2 the second half...)'
        ]);
        return $parser;
    }

}
