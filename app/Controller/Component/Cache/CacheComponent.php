<?php

class CacheComponent {

    public $default_cache_time = 300;
    public $cache_time_exceptions = array();
    public $cache_methods = [
        'avgTicket',
        'conversionRate',
        'devices',
        'dwell',
        'footTraffic',
        'itemsPerTransaction',
        'returning',
        'revenue',
        //'sensorTraffic',
        'timeInShop',
        'totalItems',
        'totals',
        'traffic',
        'transactions',
        'walkbys',
        'windowConversion',
    ];

    private function getCacheFilePath($component, $method, $params) {
        $component = strtolower($component);
        $method = strtolower($method);
        $path = ROOT . DS . 'app' . DS . 'tmp' . DS . 'cache' . DS . 'api_calls' . DS . $component . DS . $method;
        $tmp = '';
        foreach ($params as $k => $v) {
            $tmp .= $k . ':' . $v;
        }
        return $path . DS . md5($tmp) . '.cache';
    }

    private function createCacheFolders($component, $method) {
        $component = strtolower($component);
        $method = strtolower($method);
        $path = ROOT . DS . 'app' . DS . 'tmp' . DS . 'cache' . DS . 'api_calls';
        if (!file_exists($path)) {
            mkdir($path);
        }

        $path = $path . DS . $component;
        if (!file_exists($path)) {
            mkdir($path);
        }

        $path .= DS . $method;
        if (!file_exists($path)) {
            mkdir($path);
        }
    }

    private function getPreviousResult($component, $method, $params) {
        unset($params['access_token']);
        unset($params['norollups']);
        unset($params['nocache']);
        unset($params['rollup']);
        //if ($this->cache) {
        if ($component == 'location' && $method == 'purchaseInfo') {
            $filename = $this->getCacheFilePath($component, $method, $params);
            if (file_exists($filename)) {
                $cache_time = (isset($this->cache_time_exceptions[$component][$method])) ? $this->cache_time_exceptions[$component][$method] : $this->default_cache_time;
                if (time() - filemtime($filename) <= $cache_time) {
                    include $filename;
                    return $result;
                }
            }
        } else if ($this->rollups && $component == 'location' && in_array($method, $this->cache_methods)) {
            $oModel = new Model(false, 'walkbys', 'rollups');
            $oDb = $oModel->getDataSource();
            $sSQL = <<<SQL
SELECT * 
FROM $method  
WHERE location_id = :location_id  
  AND `date` = :start_date
  AND `date` = :end_date
SQL;
            $aRes = $oDb->fetchAll($sSQL, [
                ':location_id' => $params['location_id'],
                ':start_date' => $params['start_date'],
                ':end_date' => $params['end_date']
                    ]
            );
            if (!empty($aRes)) {
                if ($method != 'totals') {
                    $data = $this->internalCall('location', 'data', array('location_id' => $params['location_id']));
                    $weekday = new DateTime($params['start_date']);
                    $weekday = strtolower(date_format($weekday, 'l'));
                    $tmp = $data['data'][$weekday . '_open'];
                    $isOpen = $tmp != 0;
                    $open = ($isOpen) ? (int) strstr($tmp, ':', true) : -1;
                    $tmp = $data['data'][$weekday . '_close'];
                    $close = ($isOpen) ? (int) strstr($tmp, ':', true) : -1;
                    $to_return = [
                        'data' => [
                            'totals' => [
                                'open' => $aRes[0][$method]['total_open'],
                                'close' => $aRes[0][$method]['total_close'],
                                'total' => $aRes[0][$method]['total_total']
                            ],
                            'breakdown' => [
                                $params['start_date'] => [
                                    'hours' => [
                                        '00' => ['open' => ($isOpen ? (0 >= $open && 0 <= $close) : false), 'total' => $aRes[0][$method]['h00']],
                                        '01' => ['open' => ($isOpen ? (1 >= $open && 1 <= $close) : false), 'total' => $aRes[0][$method]['h01']],
                                        '02' => ['open' => ($isOpen ? (2 >= $open && 2 <= $close) : false), 'total' => $aRes[0][$method]['h02']],
                                        '03' => ['open' => ($isOpen ? (3 >= $open && 3 <= $close) : false), 'total' => $aRes[0][$method]['h03']],
                                        '04' => ['open' => ($isOpen ? (4 >= $open && 4 <= $close) : false), 'total' => $aRes[0][$method]['h04']],
                                        '05' => ['open' => ($isOpen ? (5 >= $open && 5 <= $close) : false), 'total' => $aRes[0][$method]['h05']],
                                        '06' => ['open' => ($isOpen ? (6 >= $open && 6 <= $close) : false), 'total' => $aRes[0][$method]['h06']],
                                        '07' => ['open' => ($isOpen ? (7 >= $open && 7 <= $close) : false), 'total' => $aRes[0][$method]['h07']],
                                        '08' => ['open' => ($isOpen ? (8 >= $open && 8 <= $close) : false), 'total' => $aRes[0][$method]['h08']],
                                        '09' => ['open' => ($isOpen ? (9 >= $open && 9 <= $close) : false), 'total' => $aRes[0][$method]['h09']],
                                        '10' => ['open' => ($isOpen ? (10 >= $open && 10 <= $close) : false), 'total' => $aRes[0][$method]['h10']],
                                        '11' => ['open' => ($isOpen ? (11 >= $open && 11 <= $close) : false), 'total' => $aRes[0][$method]['h11']],
                                        '12' => ['open' => ($isOpen ? (12 >= $open && 12 <= $close) : false), 'total' => $aRes[0][$method]['h12']],
                                        '13' => ['open' => ($isOpen ? (13 >= $open && 13 <= $close) : false), 'total' => $aRes[0][$method]['h13']],
                                        '14' => ['open' => ($isOpen ? (14 >= $open && 14 <= $close) : false), 'total' => $aRes[0][$method]['h14']],
                                        '15' => ['open' => ($isOpen ? (15 >= $open && 15 <= $close) : false), 'total' => $aRes[0][$method]['h15']],
                                        '16' => ['open' => ($isOpen ? (16 >= $open && 16 <= $close) : false), 'total' => $aRes[0][$method]['h16']],
                                        '17' => ['open' => ($isOpen ? (17 >= $open && 17 <= $close) : false), 'total' => $aRes[0][$method]['h17']],
                                        '18' => ['open' => ($isOpen ? (18 >= $open && 18 <= $close) : false), 'total' => $aRes[0][$method]['h18']],
                                        '19' => ['open' => ($isOpen ? (19 >= $open && 19 <= $close) : false), 'total' => $aRes[0][$method]['h19']],
                                        '20' => ['open' => ($isOpen ? (20 >= $open && 20 <= $close) : false), 'total' => $aRes[0][$method]['h20']],
                                        '21' => ['open' => ($isOpen ? (21 >= $open && 21 <= $close) : false), 'total' => $aRes[0][$method]['h21']],
                                        '22' => ['open' => ($isOpen ? (22 >= $open && 22 <= $close) : false), 'total' => $aRes[0][$method]['h22']],
                                        '23' => ['open' => ($isOpen ? (23 >= $open && 23 <= $close) : false), 'total' => $aRes[0][$method]['h23']],
                                    ],
                                    'totals' => [
                                        'isOpen' => $isOpen,
                                        'close' => $aRes[0][$method]['total_close'],
                                        'total' => $aRes[0][$method]['total_total'],
                                        'open' => $aRes[0][$method]['total_open'],
                                    ]
                                ]
                            ],
                        ],
                        'options' => [
                            'endpoint' => '/' . $component . '/' . $method,
                            'location_id' => $params['location_id'],
                            'start_date' => $params['start_date'],
                            'end_date' => $params['end_date']
                        ]
                    ];
                    return APIComponent::nightClubFormat($to_return, $data);
                } else {
                    return [
                        'walkbys' => $aRes[0][$method]['walkbys'],
                        'sensorTraffic' => $aRes[0][$method]['sensorTraffic'],
                        'transactions' => $aRes[0][$method]['transactions'],
                        'revenue' => $aRes[0][$method]['revenue'],
                        'totalItems' => $aRes[0][$method]['totalItems'],
                        'returning' => $aRes[0][$method]['returning'],
                        'footTraffic' => $aRes[0][$method]['footTraffic'],
                        'timeInShop' => $aRes[0][$method]['timeInShop'],
                        'traffic' => $aRes[0][$method]['traffic'],
                        'devices' => $aRes[0][$method]['devices'],
                        'itemsPerTransaction' => $aRes[0][$method]['itemsPerTransaction'],
                        'windowConversion' => $aRes[0][$method]['windowConversion'],
                        'avgTicket' => $aRes[0][$method]['avgTicket'],
                        'conversionRate' => $aRes[0][$method]['conversionRate'],
                        'dwell' => $aRes[0][$method]['dwell'],
                    ];
                }
            }
        }
        return false;
    }

    private function cache($component, $method, $params, $result, $from_rollups = false) {
        if (!empty($result) && ($component . '/' . $method) != 'location/data') {
            unset($params['access_token']);
            unset($params['norollups']);
            unset($params['nocache']);
            unset($params['rollup']);
            if (
                    isset($params['start_date']) &&
                    isset($params['end_date']) &&
                    $params['start_date'] != $params['end_date']
            ) {
                return;
            }

            //if ($this->cache) {
            if ($component == 'location' && $method == 'purchaseInfo') {
                $this->createCacheFolders($component, $method);
                $cache_file = $this->getCacheFilePath($component, $method, $params);
                $handle = fopen($cache_file, 'w+');
                fwrite($handle, '<?php $result = ' . var_export($result, true) . ';?>');
                fclose($handle);
            } else if ($this->rollups) {
                if (!$from_rollups && $component == 'location' && in_array($method, $this->cache_methods)) {
                    $date = $params['start_date'];
                    $location_id = $params['location_id'];
                    $oModel = new Model(false, 'walkbys', 'rollups');
                    $oDb = $oModel->getDataSource();
                    $sSQL = "SELECT id FROM $method WHERE location_id = :location_id AND `date` = :date";
                    $aRes = $oDb->fetchAll($sSQL, [
                        ':location_id' => $location_id,
                        ':date' => $date
                    ]);
                    if (empty($aRes)) {
                        if ($method != 'totals') {
                            $sSQL = "
INSERT IGNORE INTO $method
    SET date = '$date',
        location_id = $location_id,
        total_open = " . coalesce($result['data']['breakdown'][$date]['totals']['open'], 0) . ",
        total_close = " . coalesce($result['data']['breakdown'][$date]['totals']['close'], 0) . ",
        total_total = " . coalesce($result['data']['breakdown'][$date]['totals']['total'], 0) . ",
        h00 = " . coalesce($result['data']['breakdown'][$date]['hours']['00']['total'], 0) . ",
        h01 = " . coalesce($result['data']['breakdown'][$date]['hours']['01']['total'], 0) . ",
        h02 = " . coalesce($result['data']['breakdown'][$date]['hours']['02']['total'], 0) . ",
        h03 = " . coalesce($result['data']['breakdown'][$date]['hours']['03']['total'], 0) . ",
        h04 = " . coalesce($result['data']['breakdown'][$date]['hours']['04']['total'], 0) . ",
        h05 = " . coalesce($result['data']['breakdown'][$date]['hours']['05']['total'], 0) . ",
        h06 = " . coalesce($result['data']['breakdown'][$date]['hours']['06']['total'], 0) . ",
        h07 = " . coalesce($result['data']['breakdown'][$date]['hours']['07']['total'], 0) . ",
        h08 = " . coalesce($result['data']['breakdown'][$date]['hours']['08']['total'], 0) . ",
        h09 = " . coalesce($result['data']['breakdown'][$date]['hours']['09']['total'], 0) . ",
        h10 = " . coalesce($result['data']['breakdown'][$date]['hours']['10']['total'], 0) . ",
        h11 = " . coalesce($result['data']['breakdown'][$date]['hours']['11']['total'], 0) . ",
        h12 = " . coalesce($result['data']['breakdown'][$date]['hours']['12']['total'], 0) . ",
        h13 = " . coalesce($result['data']['breakdown'][$date]['hours']['13']['total'], 0) . ",
        h14 = " . coalesce($result['data']['breakdown'][$date]['hours']['14']['total'], 0) . ",
        h15 = " . coalesce($result['data']['breakdown'][$date]['hours']['15']['total'], 0) . ",
        h16 = " . coalesce($result['data']['breakdown'][$date]['hours']['16']['total'], 0) . ",
        h17 = " . coalesce($result['data']['breakdown'][$date]['hours']['17']['total'], 0) . ",
        h18 = " . coalesce($result['data']['breakdown'][$date]['hours']['18']['total'], 0) . ",
        h19 = " . coalesce($result['data']['breakdown'][$date]['hours']['19']['total'], 0) . ",
        h20 = " . coalesce($result['data']['breakdown'][$date]['hours']['20']['total'], 0) . ",
        h21 = " . coalesce($result['data']['breakdown'][$date]['hours']['21']['total'], 0) . ",
        h22 = " . coalesce($result['data']['breakdown'][$date]['hours']['22']['total'], 0) . ",
        h23 = " . coalesce($result['data']['breakdown'][$date]['hours']['23']['total'], 0) . ",
        ts_creation = NOW(),
        ts_update = NOW()
";
                        } else {
                            $sSQL = <<<SQL
INSERT IGNORE INTO $method
    SET location_id         = {$params['location_id']},
        date                = '{$params['start_date']}',
        walkbys             = {$result['walkbys']},
        sensorTraffic       = {$result['sensorTraffic']},
        transactions        = {$result['transactions']},
        revenue             = {$result['revenue']},
        totalItems          = {$result['totalItems']},
        returning           = {$result['returning']},
        footTraffic         = {$result['footTraffic']},
        timeInShop          = {$result['timeInShop']},
        traffic             = {$result['traffic']},
        devices             = {$result['devices']},
        itemsPerTransaction = {$result['itemsPerTransaction']},
        windowConversion    = {$result['windowConversion']},
        avgTicket           = {$result['avgTicket']},
        conversionRate      = {$result['conversionRate']},
        dwell               = {$result['dwell']},
        ts_creation         = NOW(),        
        ts_update           = NOW()        
SQL;
                        }
                        $oDb->query($sSQL);
                    } else {
                        //throw new APIException(500, 'duplicated_cache', "This request is already cached");
                    }
                }
            }
        }
    }

}
