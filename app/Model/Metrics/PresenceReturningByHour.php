<?php

App::uses('CacheMetricModel', 'Model');

class PresenceReturningByHour extends CacheMetricModel
{

    public $useTable = 'presenceReturningByHour';

    public function getFromRaw ()
    {
        if (!$this->validates()) {            
            throw new Swarm\ApplicationErrorException(SwarmErrorCodes::MODEL_NOT_INITIALIZED);
        }        
        $networkId = $this->locationSetting->getSettingValue(LocationSetting::NETWORK_ID);
        $result    = [];
        foreach (['sessions_archive', 'sessions'] as $sessionsTable) {
            if(!$this->needsSessionTable($sessionsTable)){
                continue;
            }       
            $model  = new Model(null, $sessionsTable, 'swarmdata');
            $db     = $model->getDataSource();
            $xQuery = "SELECT 0 as hour";
            for ($i = 1; $i < 24; $i++) {
                $xQuery .= ' UNION SELECT ' . $i;
            }

            $tQuery = "SELECT '{$this->data[__CLASS__]['start_date']}' as date ";
            $start  = new DateTime($this->data[__CLASS__]['start_date']);
            $end    = new DateTime($this->data[__CLASS__]['end_date']);
            date_add($start, date_interval_create_from_date_string('1 days'));
            while ($start <= $end) {
                $tQuery .= " UNION SELECT '" . $start->format('Y-m-d') . "' ";
                date_add($start, date_interval_create_from_date_string('1 days'));
            }

            $yQuery   = [
                'table'      => $db->fullTableName($model),
                'alias'      => 'Session',
                'conditions' => [],
                'fields'     => [
                    "DISTINCT Session.mac_id as unique_mac",
                    "DATE_FORMAT(MAX(CONVERT_TZ(time_login,'GMT','{$this->getTimezone()}')), '%Y-%m-%d') as max_login_date",
                    "MAX(CONVERT_TZ(time_login,'GMT','{$this->getTimezone()}')) as max_login",
                    "DATE_FORMAT((CONVERT_TZ(time_login,'GMT','{$this->getTimezone()}')),'%H') as login",
                    "DATE_FORMAT((CONVERT_TZ(time_logout,'GMT','{$this->getTimezone()}')),'%H') as logout"
                ],
                'joins'      => [
                    [
                        'table'      => 'mac_address',
                        'alias'      => 'MacAddress',
                        'type'       => 'INNER',
                        'conditions' => [
                            'Session.mac_id = MacAddress.id',
                            'Session.network_id'    => $networkId,
                            'Session.sessionid'     => ['instore', 'passive', 'active', 'login'],
                            'Session.time_logout IS NOT NULL',
                            'Session.time_login >=' => $this->getStartTime(),
                            'Session.time_login <=' => $this->getEndTime(),
                            'MacAddress.status !='  => 'noise',
                            'MacAddress.noise'      => false,
                        ]
                    ]
                ],
                'group'      => ['Session.mac_id']
            ];
            $zQuery   = [
                'table'  => '(' . $db->buildStatement($yQuery, $model) . ')',
                'alias'  => 'y',
                'fields' => [ 'login', 'logout', 'max_login_date'],
                'joins'  => [
                    [
                        'table'      => 'network_mac_logins',
                        'alias'      => 'nml',
                        'conditions' => [
                            'nml.first_logout < y.max_login',
                            'nml.mac_id = y.unique_mac',
                            'nml.network_id =' => $networkId,
                            'nml.first_logout IS NOT NULL',
                            'y.max_login IS NOT NULL'
                        ]
                    ]
                ]
            ];
            $query    = [
                'table'  => '( ' . $xQuery . ' )',
                'alias'  => 'x',
                'fields' => ['x.hour', 'COUNT(z.login) as value', "t.date"],
                'joins'  => [
                    [
                        'table'      => '(' . $tQuery . ')',
                        'alias'      => 't',
                        'type'       => 'LEFT',
                        'conditions' => ['true']
                    ],
                    [
                        'table'      => '(' . $db->buildStatement($zQuery, $model) . ')',
                        'alias'      => 'z',
                        'type'       => 'LEFT',
                        'conditions' => [
                            'x.hour >= z.login',
                            'x.hour <= z.logout',
                            't.date = z.max_login_date'
                        ]
                    ]
                ],
                'group'  => ['date', 'hour']
            ];
            $querySQL = $db->buildStatement($query, $model);
            $result   = array_merge($result, $db->fetchAll($querySQL));
        }
        $return = [];
        foreach ($result as $row) {
            $return[$row['t']['date']][$row['x']['hour']] = [
                'hour'  => $row['x']['hour'],
                'value' => $row[0]['value']
            ];
        }
        return $return;
    }

    public function storeInCache ($result = [])
    {
         if (!$this->validates()) {            
            throw new Swarm\ApplicationErrorException(SwarmErrorCodes::MODEL_NOT_INITIALIZED);
        }        
        $openHours = $this->getLocationSetting()->getOpenHours();
        foreach ($result as $date => $rows) {
            $model    = new self();
            $dateTime = new DateTime($date);
            $weekday  = strtolower($dateTime->format('l'));
            $isOpen   = $openHours[$weekday]['isOpen'];
            $open     = (int) strstr($openHours[$weekday]['open'], ':', true);
            $close    = (int) strstr($openHours[$weekday]['close'], ':', true);
            $data     = MetricFormatComponent::emptyDayResult($this->data[__CLASS__]['location_id'], $date);
            foreach ($rows as $row) {
                $i              = $row['hour'];
                $h              = ($row['hour'] < 10 ? '0' : '') . $row['hour'];
                $data['h' . $h] = (!empty($row['value']))?$row['value']:0;
                $data['total_open'] += ($isOpen && $i >= $open && $i <= $close) ? $row['value'] : 0;
                $data['total_close'] += (!$isOpen || $i < $open || $i > $close) ? $row['value'] : 0;
                $data['total_total'] += $row['value'];
            }
            $row = $model->find(
                    'first', ['conditions' => [
                    'date'        => $date,
                    'location_id' => $this->data[__CLASS__]['location_id']
                ]
            ]);
            if (!empty($row)) {
                $model->read(null, $row[__CLASS__]['id']);
            }
            $model->save([__CLASS__ => $data], false, array_keys($data));
            $totals = new Totals();
            $totals->updateRollupMetric($date, $this->data[__CLASS__]['location_id'], 'presenceReturning', $data['total_open']);
        }
    }

    public function getFromCache ()
    {
        if (!$this->validates()) {            
            throw new Swarm\ApplicationErrorException(SwarmErrorCodes::MODEL_NOT_INITIALIZED);
        }        
        $result = $this->find('all', [
            'conditions' => [
                'date >='     => $this->getStartDate(),
                'date <='     => $this->getEndDate(),
                'location_id' => $this->getLocationId()
            ]
        ]);
        $return = [];
        foreach ($result as $row) {
            $return[$row[__CLASS__]['date']] = $row[__CLASS__];
        }
        return $return;
    }

}
