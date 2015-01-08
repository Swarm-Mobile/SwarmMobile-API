<?php

App::uses('MetricModel', 'Model');

class Websites extends MetricModel
{

    public $useDbConfig = 'swarmdata';
    public $useTable = 'analytics';

    public function getFromRaw ()
    {
        if (!$this->validates()) {            
            throw new Swarm\ApplicationErrorException(SwarmErrorCodes::MODEL_NOT_INITIALIZED);
        }        
        $locationSetting = $this->getLocationSetting();
        $networkId       = $locationSetting->getSettingValue(LocationSetting::NETWORK_ID);        
        if (empty($networkId)) {
            throw new Swarm\ApplicationErrorException(SwarmErrorCodes::LOCATION_WITHOUT_NETWORK);
        }        
        $db    = $this->getDataSource();

        $query    = [
            'fields'     => [
                "domain",
                "COUNT(*) as count"
            ],
            'table'      => 'analytics',
            'alias'      => 'Analytics',
            'conditions' => [
                'netid'     => $networkId,
                'domain IS NOT NULL',
                'domain !=' => '',
                'time >='   => $this->getStartTime(),
                'time <='   => $this->getEndTime()
            ],
            'group'      => ['domain'],
            'order'      => ['count DESC']
        ];
        $querySQL = $db->buildStatement($query, $this);
        $result   = $db->fetchAll($querySQL);
        $return   = [];
        foreach ($result as $row) {
            $return[] = [
                'domain' => $row['Analytics']['domain'],
                'count'  => (int)$row[0]['count'],
            ];
        }
        return $return;
    }

}
