<?php

App::uses('MetricModel', 'Model');

class Totals extends MetricModel
{

    public $useTable = 'totals';

    public function updateRollupMetric ($date, $locationId, $metric, $value)
    {
        $row  = $this->find('first', [
            'conditions' => [
                'location_id' => $locationId,
                'date'        => $date
            ]
        ]);
        $data = [$metric => $value, 'location_id' => $locationId, 'date' => $date];
        if (!empty($row)) {
            $this->read(null, $row['Totals']['id']);
        }
        else {
            $data['ts_creation'] = date('Y-m-d H:i:s');
            $data['ts_update']   = date('Y-m-d H:i:s');
        }
        $this->save(['Totals' => $data], false, array_keys($data));
    }

    public function getFromRaw ()
    {
        $result = $this->find('all', [
            'conditions' => [
                'date >='     => $this->getStartDate(),
                'date <='     => $this->getEndDate(),
                'location_id' => $this->getLocationId()
            ]
        ]);
        $return = [
            'walkbys'                => 0,
            'transactions'           => 0,                                    
            'footTraffic'            => 0,
            'presenceTraffic'        => 0,
            'presenceReturning'      => 0,
            'portalTraffic'          => 0,
            'revenue'                => 0,            
            'totalItems'             => 0,
            'conversionRate'         => 0,                                   
            'traffic'                => 0,
            'devices'                => 0,
            'timeInShop'             => 0
        ];
        foreach ($result as $row) {
            foreach ($return as $metric => $total) {
                if (isset($row['Totals'][$metric])) {
                    $return[$metric] += $row['Totals'][$metric];
                }
            }
        }
        $defaultTraffic = $this->locationSetting->getSettingValue(LocationSetting::FOOTTRAFFIC_DEFAULT_DEVICE);
        $defaultTraffic = (empty($defaultTraffic)) ? 'portal' : $defaultTraffic;

        $defaultConversionRate = $this->locationSetting->getSettingValue(LocationSetting::CONVERSIONRATE_DEFAULT_DEVICE);
        $defaultConversionRate = (empty($defaultConversionRate)) ? 'portal' : $defaultConversionRate;
        
        $possibleTransactions             = array_filter([$return['transactions'], 1]);
        $nonZeroTransactions              = array_shift($possibleTransactions);
        $return['dwell']                  = round($return['timeInShop'] / max($return['traffic'], 1), 2);
        $return['itemsPerTransaction']    = round($return['totalItems'] / $nonZeroTransactions, 2);
        $return['avgTicket']              = round($return['revenue'] / $nonZeroTransactions, 1, 2);
        $return['windowConversion']       = MetricFormatComponent::rate($return['traffic'], $return['devices']);
        $return['portalConversionRate']   = MetricFormatComponent::rate($return['transactions'], $return['portalTraffic']);
        $return['presenceConversionRate'] = MetricFormatComponent::rate($return['transactions'], $return['presenceTraffic']);
        $return['conversionRate']         = $return[$defaultConversionRate . 'ConversionRate'];
        $return['footTraffic']            = $return[$defaultTraffic . 'Traffic'];
        $return['returning']              = $return['presenceReturning'];
        
        return $return;
    }

}
