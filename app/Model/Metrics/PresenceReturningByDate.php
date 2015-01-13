<?php

App::uses('CacheMetricModel', 'Model');
App::uses('Totals', 'Model/Totals');

class PresenceReturningByDate extends CacheMetricModel
{

    public $useTable = 'presenceReturningByDate';

    public function getFromRaw ()
    {
        if (!$this->validates()) {
            throw new Swarm\ApplicationErrorException(SwarmErrorCodes::MODEL_NOT_INITIALIZED);
        }
        $networkId = $this->locationSetting->getSettingValue(LocationSetting::NETWORK_ID);
        $result    = [];
        foreach (['sessions_archive', 'sessions'] as $sessionsTable) {
            if (!$this->needsSessionTable($sessionsTable)) {
                continue;
            }
            $model    = new Model(null, $sessionsTable, 'swarmdata');
            $db       = $model->getDataSource();
            $subquery = [
                'table'      => $db->fullTableName($model),
                'alias'      => 'Session',
                'conditions' => [],
                'fields'     => [
                    "DISTINCT Session.mac_id as unique_mac",
                    "DATE(MAX(CONVERT_TZ(time_login,'GMT','{$this->getTimezone()}'))) as max_login"
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
            $query    = [
                'table'      => '(' . $db->buildStatement($subquery, $model) . ')',
                'alias'      => 'y',
                'conditions' => [],
                'fields'     => [
                    "DATE(y.max_login) AS date",
                    "COUNT(DISTINCT y.unique_mac) as value",
                ],
                'joins'      => [
                    [
                        'table'      => 'network_mac_logins',
                        'alias'      => 'nml',
                        'type'       => 'INNER',
                        'conditions' => [
                            'nml.first_logout < y.max_login',
                            'nml.mac_id = y.unique_mac',
                            'nml.first_logout IS NOT NULL',
                            'nml.network_id ' => $networkId,
                            'y.max_login IS NOT NULL',
                        ]
                    ]
                ],
                'group'      => ['date']
            ];
            $querySQL = $db->buildStatement($query, $model);
            $result   = array_merge($result, $db->fetchAll($querySQL));
        }
        $return = [];
        foreach ($result as $row) {
            $return[$row[0]['date']][] = $row[0];
        }
        return $return;
    }

    public function storeInCache ($result = [])
    {
        if (!$this->validates()) {
            throw new Swarm\ApplicationErrorException(SwarmErrorCodes::MODEL_NOT_INITIALIZED);
        }
        foreach ($result as $date => $rows) {
            foreach ($rows as $row) {
                $data  = [
                    'location_id' => $this->data[__CLASS__]['location_id'],
                    'date'        => $date,
                    'total'       => $row['value'],
                    'ts_creation' => date('Y-m-d H:i:s'),
                    'ts_update'   => date('Y-m-d H:i:s'),
                ];
                $model = new self();
                $row   = $model->find(
                        'first', ['conditions' => [
                        'date'        => $date,
                        'location_id' => $this->data[__CLASS__]['location_id']
                    ]
                ]);
                if (!empty($row)) {
                    $model->read(null, $row[__CLASS__]['id']);
                }
                $model->save([__CLASS__ => $data], false, array_keys($data));
            }
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
