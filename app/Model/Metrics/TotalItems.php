<?php

App::uses('CacheMetricModel', 'Model');
App::uses('Totals', 'Model/Totals');

class TotalItems extends CacheMetricModel
{

    public $useTable = 'totalItems';

    public function getFromRaw ()
    {
        if (!$this->validates()) {            
            throw new Swarm\ApplicationErrorException(SwarmErrorCodes::MODEL_NOT_INITIALIZED);
        }        
        $posStoreId = $this->locationSetting->getSettingValue(LocationSetting::POS_STORE_ID);
        $register   = $this->locationSetting->getSettingValue(LocationSetting::REGISTER_FILTER);
        $outlet     = $this->locationSetting->getSettingValue(LocationSetting::OUTLET_FILTER);
        $model      = new Model(null, 'invoices', 'pos');
        $db         = $model->getDataSource();

        $subquery = [
            'table'      => $db->fullTableName($model),
            'alias'      => 'Invoice',
            'fields'     => [
                "SUM(Line.quantity) as total_items",
                "DATE_FORMAT(CONVERT_TZ(Invoice.ts,'GMT', '{$this->getTimezone()}'), '%Y-%m-%d') AS date",
                "DATE_FORMAT(CONVERT_TZ(Invoice.ts,'GMT', '{$this->getTimezone()}'), '%k') AS hour"
            ],
            'joins'      => [
                [
                    'table'      => 'invoice_lines',
                    'alias'      => 'Line',
                    'type'       => 'INNER',
                    'conditions' => ['Invoice.invoice_id = Line.invoice_id']
                ]
            ],
            'conditions' => [
                'Invoice.completed'=> true,
                'Invoice.store_id' => $posStoreId,
                'Invoice.total !=' => 0,
                'Invoice.ts >='    => $this->getStartTime(),
                'Invoice.ts <='    => $this->getEndTime(),
                (!empty($register)) ? "Invoice.register_id = $register " : '',
                (!empty($outlet)) ? "Invoice.outlet_id   = $outlet   " : '',
            ],
            'group'      => ['Invoice.invoice_id']
        ];

        $query = [
            'fields' => ['SUM(total_items) as value, date, hour'],
            'table'  => '(' . $db->buildStatement($subquery, $model) . ')',
            'alias'  => 't2',
            'group'  => ['date', 'hour']
        ];

        $querySQL = $db->buildStatement($query, $model);
        $result   = $db->fetchAll($querySQL);
        $return   = [];
        foreach ($result as $row) {
            $return[$row['t2']['date']][$row['t2']['hour']] = [
                'value' => $row[0]['value'],
                'hour'  => $row['t2']['hour'],
                'date'  => $row['t2']['date']
            ];
        }
        return $return;
    }

    public function storeInCache ($result = [])
    {
        if (!$this->validates()) {            
            throw new Swarm\ApplicationErrorException(SwarmErrorCodes::MODEL_NOT_INITIALIZED);
        }        
        $transactionsWhileClosed = $this->locationSetting->getSettingValue(LocationSetting::TRANSACTIONS_WHILE_CLOSED);
        $totalIndex              = $transactionsWhileClosed == 'no' ? 'total_open' : 'total_total';
        $openHours               = $this->getLocationSetting()->getOpenHours();
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
            $totals->updateRollupMetric($date, $this->data[__CLASS__]['location_id'], 'totalItems', $data[$totalIndex]);
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
