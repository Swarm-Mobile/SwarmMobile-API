<?php

App::uses('FootTraffic', 'Model/Metrics');
App::uses('Totals', 'Model/Totals');

class PortalTraffic extends FootTraffic
{

    public $useTable = 'portalTraffic';

    public function getFromRaw ()
    {        
        if (!$this->validates()) {            
            throw new Swarm\ApplicationErrorException(SwarmErrorCodes::MODEL_NOT_INITIALIZED);
        }        
        $model    = new Model(null, 'visitorEvent', 'portal');
        $db       = $model->getDataSource();
        $query    = [
            'table'      => 'visitorEvent',
            'alias'      => 'VisitorEvent',
            'fields'     => [
                'SUM(entered) AS value',
                "DATE_FORMAT(CONVERT_TZ(ts,'GMT', '{$this->getTimezone()}'), '%Y-%m-%d') AS date",
                "DATE_FORMAT(CONVERT_TZ(ts,'GMT', '{$this->getTimezone()}'), '%k') AS hour"
            ],
            'conditions' => [
                'location_id' => $this->getLocationId(),
                'ts >='       => $this->getStartTime(),
                'ts <='       => $this->getEndTime()
            ],
            'group'      => ['date', 'hour']
        ];
        $querySQL = $db->buildStatement($query, $this);
        $result   = $db->fetchAll($querySQL);
        $return   = [];
        foreach ($result as $row) {
            $return[$row[0]['date']][$row[0]['hour']] = $row[0];
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
                $data['h' . $h] = (!empty($row['value'])) ? $row['value'] : 0;
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
            $totals->updateRollupMetric($date, $this->data[__CLASS__]['location_id'], 'portalTraffic', $data['total_open']);
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
