<?php

App::uses('DBComponent', 'Controller/Component');
App::uses('APIComponent', 'Controller/Component');

class LocationComponent extends APIComponent
{

    public $post_actions   = ['create', 'updateSettings'];
    public $put_actions    = [];
    public $delete_actions = [];

    //IOS ENDPOINTS
    public function whereAmI ($params)
    {
        
    }

    public function whatIsHere ($params)
    {
        
    }

    public function monthlyTotals ($params)
    {
        $rules = array (
            'location_id' => array ('required', 'int'),
            'year'        => array ('required', 'int'),
            'month'       => array ('required', 'int')
        );
        $this->validate($params, $rules);

        $start_date = $params['year'] . '-' . $params['month'] . '-01';
        $end_date   = $params['year'] . '-' . ($params['month'] + 1) . '-01';

        $end      = new DateTime($end_date);
        date_sub($end, date_interval_create_from_date_string('1 days'));
        $end_date = date_format($end, 'Y-m-d');

        $start = new DateTime($start_date);

        $revenue  = array ();
        $visitors = array ();

        $result = [
            'data'    => [
                'breakdown' => [],
                'totals'    => [
                    'revenue'                => 0,
                    'visitors'               => 0,
                    'conversionRate'         => 0,
                    'avgRevenueDaily'        => 0,
                    'avgVisitorsDaily'       => 0,
                    'avgConversionRateDaily' => 0,
                ]
            ],
            'options' => [
                'endpoint'    => '/location/monthlyTotals',
                'year'        => $params['year'],
                'month'       => $params['month'],
                'location_id' => $params['location_id']
            ]
        ];
        unset($params['month']);
        unset($params['year']);
        $params['start_date'] = $start_date;
        $params['end_date']   = $end_date;

        $slave_params = $params;
        
        $data     = $this->api->internalCall('location', 'data', array ('location_id' => $params['location_id']));
        if(!empty($data)) {
            $while_closed = coalesce($data['data']['transactions_while_closed'], '');
            $open_total   = $while_closed == 'no' ? 'open' : 'total';
            $timezone = coalesce($data['data']['timezone'], 'America/Los_Angeles');
    
            $register_filter = (!empty($data['data']['register_filter'])) ? " AND i.register_id =" . $data['data']['register_filter'] . " " : '';
            $outlet_filter   = (!empty($data['data']['outlet_filter'])) ? " AND i.outlet_id =" . $data['data']['outlet_filter']. " " : '';
    
            $lightspeed_id = coalesce($data['data']['lightspeed_id'], 0);
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
    
            $oDb  = DBComponent::getInstance('invoices', 'pos');
            $sSQL = <<<SQL
SELECT SUM(total) as revenue, count(*) as transactions, date(ts) as date
FROM invoices i
WHERE store_id = $lightspeed_id
  $outlet_filter
  $register_filter
  AND i.completed 
  AND i.total != 0 
  AND ts BETWEEN '$start_date' AND '$end_date'
GROUP BY date(ts)
ORDER BY date ASC
SQL;
            $aPOS = $oDb->fetchAll($sSQL);
    
            $oDb     = DBComponent::getInstance('visitorEvent', 'portal');
            $sSQL    = <<<SQL
SELECT SUM(entered) as visitors, date(ts) as date
FROM visitorEvent
WHERE location_id = {$params['location_id']}
  AND ts BETWEEN '$start_date' AND '$end_date'
GROUP BY date(ts)
ORDER BY date ASC
SQL;
            $aPortal = $oDb->fetchAll($sSQL);
    
            $days  = ['byWeek' => [], 'total' => 0];
            $weeks = [];
            do {
                $transactions             = 0;
                $revenue                  = 0;
                $visitors                 = 0;
                $slave_params['end_date'] = $slave_params['start_date'];
    
                foreach ($aPOS as $k => $oRow) {
                    if ($oRow[0]['date'] == date_format($start, 'Y-m-d')) {
                        $revenue      = $oRow[0]['revenue'];
                        $transactions = $oRow[0]['transactions'];
                        unset($aPOS[$k]);
                        break;
                    }
                }
                foreach ($aPortal as $k => $oRow) {
                    if ($oRow[0]['date'] == date_format($start, 'Y-m-d')) {
                        $visitors = $oRow[0]['visitors'];
                        unset($aPortal[$k]);
                        break;
                    }
                }
    
                $days['total'] ++;
    
                if (!isset($days['byWeek'][date_format($start, 'W')])) {
                    $days['byWeek'][date_format($start, 'W')] = 0;
                    $weeks[date_format($start, 'W')]['start'] = date_format($start, 'Y-m-d');
                }
                $weeks[date_format($start, 'W')]['end'] = date_format($start, 'Y-m-d');
    
                $days['byWeek'][date_format($start, 'W')] ++;
                if (!isset($result['data']['breakdown'][date_format($start, 'W')])) {
                    $result['data']['breakdown'][date_format($start, 'W')] = [
                        'revenue'                => 0,
                        'visitors'               => 0,
                        'conversionRate'         => 0,
                        'avgRevenueDaily'        => 0,
                        'avgVisitorsDaily'       => 0,
                        'avgConversionRateDaily' => 0
                    ];
                }
                $result['data']['breakdown'][date_format($start, 'W')]['revenue'] += $revenue;
                $result['data']['breakdown'][date_format($start, 'W')]['visitors'] += $visitors;
                $result['data']['breakdown'][date_format($start, 'W')]['conversionRate'] += $transactions;
                $result['data']['totals']['revenue'] += $revenue;
                $result['data']['totals']['visitors'] += $visitors;
                $result['data']['totals']['conversionRate'] += $transactions;
    
                date_add($start, date_interval_create_from_date_string('1 days'));
                $slave_params['start_date'] = date_format($start, 'Y-m-d');
            }
            while ($start <= $end);
    
            foreach ($days['byWeek'] as $w => $c) {
                $result['data']['breakdown'][$w]['start_date']       = $weeks[$w]['start'];
                $result['data']['breakdown'][$w]['end_date']         = $weeks[$w]['end'];
                $result['data']['breakdown'][$w]['avgRevenueDaily']  = round($result['data']['breakdown'][$w]['revenue'] / coalesce($c, 1), 2);
                $result['data']['breakdown'][$w]['avgVisitorsDaily'] = round($result['data']['breakdown'][$w]['visitors'] / coalesce($c, 1), 2);
    
                $result['data']['breakdown'][$w]['avgConversionRateDaily'] = $result['data']['breakdown'][$w]['conversionRate'] / coalesce($c, 1)  ;
                $result['data']['breakdown'][$w]['avgConversionRateDaily'] = min([100, round(($result['data']['breakdown'][$w]['avgConversionRateDaily'] / coalesce($result['data']['breakdown'][$w]['avgVisitorsDaily'], 1)) * 100, 2)]);
                $result['data']['breakdown'][$w]['conversionRate']         = $result['data']['breakdown'][$w]['avgConversionRateDaily'];
            }
            $result['data']['totals']['conversionRate']   = min([100, $result['data']['totals']['conversionRate'] / coalesce($result['data']['totals']['visitors'], 1)]);
            $result['data']['totals']['avgRevenueDaily']  = round($result['data']['totals']['revenue'] / coalesce($days['total'], 1), 2);
            $result['data']['totals']['avgVisitorsDaily'] = round($result['data']['totals']['visitors'] / coalesce($days['total'], 1), 2);
    
            $result['data']['totals']['avgConversionRateDaily'] = $result['data']['totals']['conversionRate'] / coalesce($days['total'], 1);
            $result['data']['totals']['avgConversionRateDaily'] = min([100, round(($result['data']['totals']['avgConversionRateDaily'] / coalesce($result['data']['breakdown'][$w]['avgVisitorsDaily'], 1)) * 100, 2)]);
        }
        return $result;
    }

    public function historicalTotals ($params)
    {
        $rules = array ('location_id' => array ('required', 'int'));
        $this->validate($params, $rules);

        $result = [
            'data'    => [
                'totals' => [
                    'revenue'                  => 0,
                    'visitors'                 => 0,
                    'conversionRate'           => 0,
                    'avgRevenueDaily'          => 0,
                    'avgRevenueWeekly'         => 0,
                    'avgRevenueMonthly'        => 0,
                    'avgVisitorsDaily'         => 0,
                    'avgVisitorsWeekly'        => 0,
                    'avgVisitorsMonthly'       => 0,
                    'avgConversionRateDaily'   => 0,
                    'avgConversionRateWeekly'  => 0,
                    'avgConversionRateMonthly' => 0,
                ]
            ],
            'options' => [
                'endpoint'    => '/location/historicalTotals',
                'location_id' => $params['location_id']
            ]
        ];

        $data     = $this->api->internalCall('location', 'data', array ('location_id' => $params['location_id']));
        $timezone = $data['data']['timezone'];

        $start_date = firstSensor($params['location_id'], $timezone);
        if (empty($start_date)) {
            return $result;
        }

        $end_date = date('Y-m-d');
        $end      = new DateTime($end_date);
        $start    = new DateTime($start_date);

        $revenue  = array ();
        $visitors = array ();

        unset($params['month']);
        unset($params['year']);

        $params['start_date'] = $start_date;
        $params['end_date']   = $end_date;

        $days    = ['byWeek' => [], 'total' => 0];
        $weeks   = [];
        $cMonth  = date_format($start, 'm');
        $tMonth  = $cMonth;
        $nMonths = 1;

        do {
            if ($tMonth != $cMonth) {
                $nMonths++;
            }
            $tMonth = $cMonth;
            $days['total'] ++;
            if (!isset($days['byWeek'][date_format($start, 'W')])) {
                $days['byWeek'][date_format($start, 'W')] = 0;
                $weeks[date_format($start, 'W')]['start'] = date_format($start, 'Y-m-d');
            }
            $weeks[date_format($start, 'W')]['end'] = date_format($start, 'Y-m-d');
            $days['byWeek'][date_format($start, 'W')] ++;
            date_add($start, date_interval_create_from_date_string('1 days'));
            $cMonth                                 = date_format($start, 'm');
        }
        while ($start <= $end);

        $register_filter = (!empty($data['data']['register_filter'])) ? " AND i.register_id =" . $data['data']['register_filter']. " " : '';
        $outlet_filter   = (!empty($data['data']['outlet_filter'])) ?  " AND i.outlet_id =" . $data['data']['outlet_filter'] . " " : '';

        $lightspeed_id = (empty($data['data']['lightspeed_id'])) ? 0 : $data['data']['lightspeed_id'];
        list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);

        $oDb  = DBComponent::getInstance('invoices', 'pos');
        $sSQL = <<<SQL
SELECT SUM(total) as revenue, count(*) as transactions
FROM invoices i
WHERE store_id = $lightspeed_id
  $outlet_filter
  $register_filter
  AND i.completed 
  AND i.total != 0 
  AND ts BETWEEN '$start_date' AND '$end_date'
SQL;
        $aRes = $oDb->fetchAll($sSQL);

        $result['data']['totals']['revenue'] += (!empty($aRes[0][0]['revenue'])) ? $aRes[0][0]['revenue'] : 0;
        $result['data']['totals']['transactions'] += (!empty($aRes[0][0]['transactions'])) ? $aRes[0][0]['transactions'] : 0;
        $result['data']['totals']['conversionRate'] += (!empty($aRes[0][0]['transactions'])) ? $aRes[0][0]['transactions'] : 0;

        $oDb  = DBComponent::getInstance('visitorEvent', 'portal');
        $sSQL = <<<SQL
SELECT SUM(entered) as visitors
FROM visitorEvent
WHERE location_id = {$params['location_id']}
  AND ts BETWEEN '$start_date' AND '$end_date'
SQL;

        $aRes = $oDb->fetchAll($sSQL);
        $result['data']['totals']['visitors'] += $aRes[0][0]['visitors'];

        $result['data']['totals']['avgRevenueDaily']  = round($result['data']['totals']['revenue'] /  coalesce($days['total'],1), 2);
        $result['data']['totals']['avgVisitorsDaily'] = round($result['data']['totals']['visitors'] /  coalesce($days['total'],1), 2);

        $result['data']['totals']['avgConversionRateDaily'] = $result['data']['totals']['conversionRate'] /  coalesce($days['total'],1);
        $result['data']['totals']['avgConversionRateDaily'] = min([100, round(($result['data']['totals']['avgConversionRateDaily'] /  coalesce($result['data']['totals']['avgVisitorsDaily'],1)) * 100, 2)]);
        $result['data']['totals']['conversionRate']         = $result['data']['totals']['avgConversionRateDaily'];

        $result['data']['totals']['avgRevenueWeekly']  = round($result['data']['totals']['revenue'] /  coalesce(count($days['byWeek']),1), 2);
        $result['data']['totals']['avgVisitorsWeekly'] = round($result['data']['totals']['visitors'] /  coalesce(count($days['byWeek']),1), 2);

        $result['data']['totals']['avgConversionRateWeekly'] = $result['data']['totals']['conversionRate'] /  coalesce(count($days['byWeek']),1);
        $result['data']['totals']['avgConversionRateWeekly'] = min([100, round(($result['data']['totals']['avgConversionRateWeekly'] /  coalesce($result['data']['totals']['avgVisitorsWeekly'],1)) * 100, 2)]);

        $result['data']['totals']['avgRevenueMonthly']  = round($result['data']['totals']['revenue'] /  coalesce($nMonths,1), 2);
        $result['data']['totals']['avgVisitorsMonthly'] = min([100, round($result['data']['totals']['visitors'] /  coalesce($nMonths,1), 2)]);

        $result['data']['totals']['avgConversionRateMonthly'] = $result['data']['totals']['conversionRate'] /  coalesce($nMonths,1);
        $result['data']['totals']['avgConversionRateMonthly'] = min([100, round(($result['data']['totals']['avgConversionRateMonthly'] /  coalesce($result['data']['totals']['avgVisitorsMonthly'],1)) * 100, 2)]);

        return $result;
    }

    //OTHER ENDPOINTS

    public function openHours ($params)
    {
        $data   = $this->api->internalCall('location', 'data', $params);
        $return = $this->weekdays;
        foreach ($return as &$v) {
            $v = array (
                $v => array (
                    'open'  => $data['data'][$v . '_open'],
                    'close' => $data['data'][$v . '_close']
                )
            );
        }
        $result            = array ('data' => $return);
        $result['options'] = array (
            'endpoint'    => 'location/openHours',
            'location_id' => $params['location_id'],
            'start_date'  => $params['start_date'],
            'end_date'    => $params['end_date'],
        );
        return $result;
    }

    public function totals ($params)
    {
        $rules        = array (
            'location_id' => array ('required', 'int'),
            'start_date'  => array ('required', 'date', 'date_interval'),
            'end_date'    => array ('required', 'date')
        );
        $this->validate($params, $rules);
        $data         = $this->api->internalCall('location', 'data', array ('location_id' => $params['location_id']));
        $while_closed = $data['data']['transactions_while_closed'];
        $open_total   = $while_closed == 'no' ? 'open' : 'total';
        $result       = array ();
        $aPath        = array ();
        $aPostfields  = array ();
        $metrics      = [
            ['location', 'walkbys', 'open'],
            ['location', 'transactions', $open_total],
            ['location', 'dwell', 'open'],
            ['location', 'windowConversion', 'open'],
            ['location', 'returning', 'total'],
            ['location', 'footTraffic', 'total'],
            ['location', 'presenceTraffic', 'total'],
            ['location', 'portalTraffic', 'total'],
            ['location', 'revenue', $open_total],
            ['location', 'avgTicket', $open_total],
            ['location', 'totalItems', $open_total],
            ['location', 'conversionRate', $open_total],
            ['location', 'portalConversionRate', $open_total],
            ['location', 'presenceConversionRate', $open_total],
            ['location', 'itemsPerTransaction', $open_total],
            ['location', 'traffic', 'total'],
            ['location', 'devices', 'total'],
            ['location', 'timeInShop', 'total'],
            ['location', 'totalItems', $open_total],
        ];
        if ($params['start_date'] != $params['end_date']) {
            $result                           = $this->iterativeTotals('location', __FUNCTION__, $params);
            $result['dwell']                  = round($result['timeInShop'] / coalesce($result['traffic'], 1), 2);
            $result['windowConversion']       = min(100, round(($result['traffic'] / coalesce($result['devices'], coalesce($result['traffic'], 1))) * 100, 2));
            $result['conversionRate']         = min(100, round(($result['transactions'] / coalesce($result['footTraffic'], coalesce($result['transactions'], 1))) * 100, 2));
            $result['portalConversionRate']   = min(100, round(($result['transactions'] / coalesce($result['portalTraffic'], coalesce($result['transactions'], 1))) * 100, 2));
            $result['presenceConversionRate'] = min(100, round(($result['transactions'] / coalesce($result['presenceTraffic'], coalesce($result['transactions'], 1))) * 100, 2));
            $result['itemsPerTransaction']    = round($result['totalItems'] / coalesce($result['transactions'], 1), 2);
            $result['avgTicket']              = round($result['revenue'] / coalesce($result['transactions'], 1), 2);
            return $result;
        }
        else {
            $weekday = strtolower(date('l', strtotime($params['start_date'])));
            $isOpen  = $data['data'][$weekday . '_open'] != 0 && $data['data'][$weekday . '_close'] != 0;
            foreach ($metrics as $k => $v) {
                if ($isOpen) {
                    $tmp           = $this->api->internalCall($v[0], $v[1], $params);
                    $result[$v[1]] = $tmp['data']['totals'][$v[2]];
                }
                else {
                    $result[$v[1]] = 0;
                }
            }
            return $result;
        }
    }

    public function walkbys ($params)
    {
        $rules = array (
            'location_id' => array ('required', 'int'),
            'start_date'  => array ('required', 'date', 'date_interval'),
            'end_date'    => array ('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('location', __FUNCTION__, $params);
        }
        else {
            $data     = $this->api->internalCall('location', 'data', array ('location_id' => $params['location_id']));
            $ap_id    = (!empty($data['data']['ap_id'])) ? $data['data']['ap_id'] : 0;
            $timezone = $data['data']['timezone'];
            $factor   = $data['data']['traffic_factor'];
            $factor   = 1 + ((empty($factor) ? 0 : $factor / 100));
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $table    = $this->getSessionsTableName($start_date, $end_date, $params['location_id'], $ap_id);
            $oDb      = DBComponent::getInstance($table, 'swarmdata');
            $sSQL     = <<<SQL
SELECT 
    ROUND(COUNT(walkbys)*$factor) as value, 
    hour, 
    date
FROM(
    SELECT 
        DISTINCT ses1.mac_id as walkbys,        
        DATE_FORMAT(convert_tz(time_login,'GMT', '$timezone'), '%Y-%m-%d') AS date,
        DATE_FORMAT(convert_tz(time_login,'GMT', '$timezone'), '%k') AS hour
    FROM $table ses1
    INNER JOIN mac_address 
        ON ses1.mac_id = mac_address.id
    WHERE ( status !='noise' AND NOISE is false) 
      AND (network_id= $ap_id) 
      AND (sessionid='passerby') 
      AND time_login BETWEEN '$start_date' AND '$end_date'
    GROUP BY ses1.mac_id
) as t2 GROUP BY date ASC, hour ASC             
SQL;
            $aRes     = $oDb->fetchAll($sSQL);
            return $this->format($aRes, $data, $params, '/location/' . __FUNCTION__, 0, 't2');
        }
    }

    /**
     * Alias of portalTraffic
     * @param $params Array containing location_id, start_date and end_date
     * @return array Array of results formatted for display in the dashboard
     */
    public function sensorTraffic ($params)
    {
        $result                        = $this->portalTraffic($params);
        $result['options']['endpoint'] = '/location/' . __FUNCTION__;
        return $result;
    }

    public function purchaseInfo ($params)
    {
        $rules = array (
            'location_id' => array ('required', 'int'),
            'start_date'  => array ('required', 'date', 'date_interval'),
            'end_date'    => array ('required', 'date')
        );
        $this->validate($params, $rules);
        if (($params['start_date'] != $params['end_date']) && $this->api->iterative) {
            return $this->iterativeQuery('location', __FUNCTION__, $params);
        }
        else {
            $data     = $this->api->internalCall('location', 'data', array ('location_id' => $params['location_id']));
            $timezone = $data['data']['timezone'];

            $register_filter = @$data['data']['register_filter'];
            $register_filter = (!empty($register_filter)) ? " AND i.register_id = $register_filter " : '';
            $outlet_filter   = @$data['data']['outlet_filter'];
            $outlet_filter   = (!empty($outlet_filter)) ? " AND i.outlet_id = $outlet_filter " : '';

            $lightspeed_id = (empty($data['data']['lightspeed_id'])) ? 0 : $data['data']['lightspeed_id'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $table         = 'invoices';
            $oDb           = DBComponent::getInstance($table, 'pos');
            $aRes          = array ();
            if (!empty($lightspeed_id)) {
                $sSQL = <<<SQL
SELECT 
    COUNT(*) as transactions,
    SUM(revenue) as revenue,
    SUM(total_items) as total_items,
    date,
    hour
FROM (
    SELECT
            i.invoice_id as transactions,
        i.total as revenue,
        SUM(il.quantity) as total_items,
        DATE_FORMAT(CONVERT_TZ(i.ts,'GMT','$timezone'),'%Y-%m-%d' ) AS date,
        DATE_FORMAT(CONVERT_TZ(i.ts,'GMT','$timezone'), '%k') AS hour
        FROM invoices i
    LEFT JOIN invoice_lines il ON i.invoice_id = il.invoice_id
    WHERE i.store_id= $lightspeed_id
        AND i.completed 
        AND i.total != 0 
            $register_filter
            $outlet_filter
        AND i.ts BETWEEN '$start_date' AND '$end_date'
    GROUP BY i.invoice_id
) t2
GROUP BY date ASC, hour ASC             
SQL;
                $aRes = $oDb->fetchAll($sSQL);
            }
            return $aRes;
        }
    }

    public function transactions ($params)
    {
        $rules = array (
            'location_id' => array ('required', 'int'),
            'start_date'  => array ('required', 'date', 'date_interval'),
            'end_date'    => array ('required', 'date')
        );
        $this->validate($params, $rules);
        if (($params['start_date'] != $params['end_date']) && $this->api->iterative) {
            return $this->iterativeCall('location', __FUNCTION__, $params);
        }
        else {
            $data     = $this->api->internalCall('location', 'data', array ('location_id' => $params['location_id']));
            $aRes     = $this->api->internalCall('location', 'purchaseInfo', $params);
            $timezone = $data['data']['timezone'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            return $this->format($aRes, $data, $params, '/location/' . __FUNCTION__, 0, 't2', __FUNCTION__);
        }
    }

    public function revenue ($params)
    {
        $rules = array (
            'location_id' => array ('required', 'int'),
            'start_date'  => array ('required', 'date', 'date_interval'),
            'end_date'    => array ('required', 'date')
        );
        $this->validate($params, $rules);
        if (($params['start_date'] != $params['end_date']) && $this->api->iterative) {
            return $this->iterativeCall('location', __FUNCTION__, $params);
        }
        else {
            $data     = $this->api->internalCall('location', 'data', array ('location_id' => $params['location_id']));
            $aRes     = $this->api->internalCall('location', 'purchaseInfo', $params);
            $timezone = $data['data']['timezone'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            return $this->format($aRes, $data, $params, '/location/' . __FUNCTION__, 0, 't2', __FUNCTION__);
        }
    }

    public function totalItems ($params)
    {
        $rules = array (
            'location_id' => array ('required', 'int'),
            'start_date'  => array ('required', 'date', 'date_interval'),
            'end_date'    => array ('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('location', __FUNCTION__, $params);
        }
        else {
            $data     = $this->api->internalCall('location', 'data', array ('location_id' => $params['location_id']));
            $aRes     = $this->api->internalCall('location', 'purchaseInfo', $params);
            $timezone = $data['data']['timezone'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            return $this->format($aRes, $data, $params, '/location/' . __FUNCTION__, 0, 't2', 'total_items');
        }
    }

    private function returningByHour ($start_date, $end_date, $timezone, $location_id, $ap_id, $factor)
    {
        $table = $this->getSessionsTableName($start_date, $end_date, $location_id, $ap_id);
        $sSQL  = <<<SQL
SELECT
    x.hour,
    ROUND(COUNT(z.login)*$factor) as value 
FROM(
    SELECT 0 as hour 
    UNION SELECT 1   UNION SELECT 2     UNION SELECT 3  
    UNION SELECT 4   UNION SELECT 5 UNION SELECT 6  
    UNION SELECT 7   UNION SELECT 8 UNION SELECT 9  
    UNION SELECT 10  UNION SELECT 11    UNION SELECT 12 
    UNION SELECT 13  UNION SELECT 14    UNION SELECT 15 
    UNION SELECT 16  UNION SELECT 17    UNION SELECT 18 
    UNION SELECT 19  UNION SELECT 20    UNION SELECT 21 
    UNION SELECT 22  UNION SELECT 23
) x
INNER JOIN (
    SELECT login,logout
    FROM (
      SELECT 
        DISTINCT ses1.mac_id as unique_mac,(CONVERT_TZ(time_login,'GMT','$timezone')) as max_login,
        DATE_FORMAT((CONVERT_TZ(time_login,'GMT','$timezone')),'%H') as login,
        DATE_FORMAT((CONVERT_TZ(time_logout,'GMT','$timezone')),'%H') as logout
      FROM $table  ses1
      INNER JOIN mac_address 
        ON ses1.mac_id = mac_address.id
      WHERE (mac_address.status<>'noise') 
        AND (sessionid='instore' OR sessionid='passive' OR sessionid='active' OR sessionid='login') 
        AND time_logout IS NOT NULL 
        AND (network_id= $ap_id)
        AND time_login BETWEEN '$start_date' AND '$end_date'
      GROUP BY ses1.mac_id
    ) y
    INNER JOIN network_mac_logins nml
        ON nml.first_logout   < y.max_login 
       AND nml.mac_id=y.unique_mac
    WHERE nml.network_id= $ap_id
      AND nml.first_logout IS NOT NULL
      AND y.max_login IS NOT NULL
) z ON x.hour BETWEEN z.login AND z.logout
GROUP BY x.hour      
ORDER BY x.hour ASC
SQL;
        $oDb   = DBComponent::getInstance($table, 'swarmdata');
        return $oDb->fetchAll($sSQL);
    }

    private function returningByDate ($date, $data, $timezone, $location_id, $ap_id, $factor)
    {
        list($start_date, $end_date) = $this->getOpenCloseTimes($date, $data, $timezone);
        $table = $this->getSessionsTableName($start_date, $end_date, $location_id, $ap_id);
        $sSQL  = <<<SQL
SELECT  
    date(y.max_login) as date,
    ROUND(COUNT(distinct y.unique_mac)*$factor) as value
FROM (
    SELECT 
    DISTINCT ses1.mac_id as unique_mac,
    date((CONVERT_TZ(time_login,'GMT','$timezone'))) as max_login 
    FROM $table  ses1
    INNER JOIN mac_address 
    ON ses1.mac_id = mac_address.id
    WHERE (mac_address.status<>'noise')
      AND (sessionid='instore' OR sessionid='passive' OR sessionid='active' OR sessionid='login') 
      AND time_login IS NOT NULL
      AND (network_id = $ap_id) 
      AND time_login BETWEEN '$start_date' AND '$end_date'  
) y
INNER JOIN network_mac_logins nml
    ON  nml.first_logout < y.max_login 
    AND nml.mac_id=y.unique_mac
WHERE nml.first_logout IS NOT NULL
  AND nml.network_id = $ap_id
  AND y.max_login IS NOT NULL
GROUP BY date       
SQL;
        $oDb   = DBComponent::getInstance($table, 'swarmdata');
        return $oDb->fetchAll($sSQL);
    }

    public function returning ($params)
    {
        $rules = array (
            'location_id' => array ('required', 'int'),
            'start_date'  => array ('required', 'date', 'date_interval'),
            'end_date'    => array ('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('location', __FUNCTION__, $params);
        }
        else {
            $data     = $this->api->internalCall('location', 'data', array ('location_id' => $params['location_id']));
            $ap_id    = (!empty($data['data']['ap_id'])) ? $data['data']['ap_id'] : 0;
            $timezone = $data['data']['timezone'];
            $factor   = $data['data']['traffic_factor'];
            $factor   = 1 + ((empty($factor) ? 0 : $factor / 100));
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $aByHour  = $this->returningByHour($start_date, $end_date, $timezone, $params['location_id'], $ap_id, $factor);
            $aByDate  = $this->returningByDate($params['start_date'], $data, $timezone, $params['location_id'], $ap_id, $factor);
            return $this->hourlyDailyFormat($aByDate, $aByHour, $data, $params, '/location/' . __FUNCTION__, 0, 'x');
        }
    }

    public function footTraffic ($params)
    {
        $rules = array (
            'location_id' => array ('required', 'int'),
            'start_date'  => array ('required', 'date', 'date_interval'),
            'end_date'    => array ('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('location', __FUNCTION__, $params);
        }
        else {
            $aDevices = getDeviceTypesInLocation($params['location_id']);
            $data     = $this->api->internalCall('location', 'data', array ('location_id' => $params['location_id']));
            $default  = $data['data']['footTraffic_default_device'];
            $default  = (empty($default)) ? 'portal' : $default;
            $timezone = $data['data']['timezone'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $firstSensor = firstSensor($params['location_id']);
            $sdate = new DateTime($firstSensor, new DateTimeZone($timezone));
            $cdate = new DateTime($start_date, new DateTimeZone($timezone));            
            if (in_array('portal', $aDevices) && $sdate < $cdate && $default == 'portal') {
                $result = $this->portalTraffic($params);
                $result['options']['device'] = 'portal';
            }
            else {
                $result = $this->presenceTraffic($params);
                $result['options']['device'] = 'presence';
            }
            $result['options']['endpoint'] = '/location/' . __FUNCTION__;
            $result['data']['breakdown'][$params['start_date']]['totals']['device'] = $result['options']['device'];
            return $result;
        }
    }

    private function presenceTrafficByHour ($start_date, $end_date, $timezone, $location_id, $ap_id, $factor)
    {
        $table = $this->getSessionsTableName($start_date, $end_date, $location_id, $ap_id);
        $sSQL  = <<<SQL
SELECT 
    x.hour,
    ROUND(COUNT(y.mac_id)*$factor) as value
FROM(
    SELECT 0 as hour 
    UNION SELECT 1   UNION SELECT 2     UNION SELECT 3  
    UNION SELECT 4   UNION SELECT 5 UNION SELECT 6  
    UNION SELECT 7   UNION SELECT 8 UNION SELECT 9  
    UNION SELECT 10  UNION SELECT 11    UNION SELECT 12 
    UNION SELECT 13  UNION SELECT 14    UNION SELECT 15 
    UNION SELECT 16  UNION SELECT 17    UNION SELECT 18 
    UNION SELECT 19  UNION SELECT 20    UNION SELECT 21 
    UNION SELECT 22  UNION SELECT 23
) x
LEFT JOIN
(
    SELECT 
        ses1.mac_id,DATE_FORMAT(MIN(convert_tz(time_login,'GMT','$timezone')), '%H') AS walk_in,
        DATE_FORMAT(max(convert_tz(time_logout,'GMT','$timezone')),'%H') AS walk_out
    FROM $table ses1
    INNER JOIN mac_address 
        ON ses1.mac_id = mac_address.id
    WHERE (mac_address.status<>'noise') 
      AND (sessionid='instore' OR sessionid='passive' OR sessionid='active' OR sessionid='login') 
      AND time_logout IS NOT NULL
      AND (network_id=$ap_id)
      AND time_login BETWEEN '$start_date' AND '$end_date'
    GROUP BY ses1.mac_id
) y ON x.hour between walk_in and walk_out 
GROUP BY x.hour
ORDER BY x.hour ASC
SQL;
        $oDb   = DBComponent::getInstance($table, 'swarmdata');
        return $oDb->fetchAll($sSQL);
    }

    private function presenceTrafficByDate ($date, $data, $timezone, $location_id, $ap_id, $factor)
    {
        list($start_date, $end_date) = $this->getOpenCloseTimes($date, $data, $timezone);
        $table = $this->getSessionsTableName($start_date, $end_date, $location_id, $ap_id);
        $sSQL  = <<<SQL
SELECT 
    DATE(CONVERT_TZ(time_login,'GMT','$timezone')) as date,
    ROUND(COUNT(DISTINCT ses1.mac_id)*$factor) as value 
FROM $table ses1
INNER JOIN mac_address 
    ON ses1.mac_id = mac_address.id
WHERE (mac_address.status<>'noise')
 AND (sessionid='instore' OR sessionid='passive' OR sessionid='active' OR sessionid='login') 
  AND time_logout IS NOT NULL
 AND (network_id= $ap_id) 
 AND time_login BETWEEN '$start_date' AND '$end_date'  
GROUP BY date
SQL;
        $oDb   = DBComponent::getInstance($table, 'swarmdata');
        return $oDb->fetchAll($sSQL);
    }

    public function presenceTraffic ($params)
    {
        $rules = array (
            'location_id' => array ('required', 'int'),
            'start_date'  => array ('required', 'date', 'date_interval'),
            'end_date'    => array ('required', 'date')
        );
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('location', __FUNCTION__, $params);
        }
        else {
            $data     = $this->api->internalCall('location', 'data', array ('location_id' => $params['location_id']));
            $ap_id    = (!empty($data['data']['ap_id'])) ? $data['data']['ap_id'] : 0;
            $timezone = $data['data']['timezone'];
            $factor   = $data['data']['traffic_factor'];
            $factor   = 1 + ((empty($factor) ? 0 : $factor / 100));
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $aByHour  = $this->presenceTrafficByHour($start_date, $end_date, $timezone, $params['location_id'], $ap_id, $factor);
            $aByDate  = $this->presenceTrafficByDate($params['start_date'], $data, $timezone, $params['location_id'], $ap_id, $factor);
            return $this->hourlyDailyFormat($aByDate, $aByHour, $data, $params, '/location/' . __FUNCTION__, 0, 'x');
        }
    }

    public function portalTraffic ($params)
    {
        // Set validation rules and validate parameters
        $rules       = array (
            'location_id' => array ('required', 'int'),
            'start_date'  => array ('required', 'date'),
            'end_date'    => array ('required', 'date')
        );
        $this->validate($params, $rules);
        $location_id = $params['location_id'];
        $data        = $this->api->internalCall('location', 'data', array ('location_id' => $location_id));
        $timezone    = $data['data']['timezone'];

        // Pass method and parameters to iteration function if the dates are different
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('location', __FUNCTION__, $params);
        }
        // apply timezone to dates entered and query for sensor detections
        list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
        $table = 'visitorEvent';
        $oDb   = DBComponent::getInstance($table, 'portal');
        $sSQL  = <<<SQL
SELECT
    ROUND(COUNT(*)) AS detect_count,
    DATE_FORMAT(convert_tz(ts,'GMT', '$timezone'), '%Y-%m-%d') AS date,
    DATE_FORMAT(convert_tz(ts,'GMT','$timezone'), '%k') AS hour
FROM visitorEvent
WHERE
    entered = 1 AND         
    location_id=$location_id AND
    ts BETWEEN '$start_date' AND '$end_date'
GROUP BY date ASC, hour ASC
SQL;
        $aRes  = $oDb->fetchAll($sSQL);
        // return formatted result
        return $this->format($aRes, $data, $params, '/location/' . __FUNCTION__, 0, 0, 'detect_count');
    }

    public function timeInShop ($params)
    {
        $rules = array (
            'location_id' => array ('required', 'int'),
            'start_date'  => array ('required', 'date', 'date_interval'),
            'end_date'    => array ('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('location', __FUNCTION__, $params);
        }
        else {
            $data     = $this->api->internalCall('location', 'data', array ('location_id' => $params['location_id']));
            $ap_id    = (!empty($data['data']['ap_id'])) ? $data['data']['ap_id'] : 0;
            $timezone = $data['data']['timezone'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $table    = $this->getSessionsTableName($start_date, $end_date, $params['location_id'], $ap_id);
            $oDb      = DBComponent::getInstance($table, 'swarmdata');
            $sSQL     = <<<SQL
 SELECT 
     SUM(dwell_time) as value,
     date,
     hour
 FROM(
    SELECT 
       ses1.mac_id,
       (MAX(UNIX_TIMESTAMP(time_logout))-MIN(UNIX_TIMESTAMP(time_login))) as dwell_time,
       DATE_FORMAT(convert_tz(time_login,'GMT', '$timezone'), '%Y-%m-%d') AS date,
       DATE_FORMAT(convert_tz(time_login,'GMT', '$timezone'), '%k') AS hour
    FROM $table as ses1
    INNER JOIN mac_address 
            ON ses1.mac_id = mac_address.id
    WHERE status !='noise' 
       AND (         
         sessionid='instore' OR 
         sessionid='passive' OR 
         sessionid='active' OR 
         sessionid='login'
       )     
       AND noise IS false 
       AND time_logout IS NOT NULL 
       AND time_login != time_logout
       AND network_id= $ap_id
       AND time_login BETWEEN '$start_date' AND '$end_date'
    GROUP BY ses1.mac_id, date, hour
    HAVING 18000 > dwell_time
 ) t2
GROUP BY date, hour      
SQL;
            $aRes     = $oDb->fetchAll($sSQL);
            return $this->format($aRes, $data, $params, '/location/' . __FUNCTION__, 0, 't2');
        }
    }

    public function traffic ($params)
    {
        $rules = array (
            'location_id' => array ('required', 'int'),
            'start_date'  => array ('required', 'date', 'date_interval'),
            'end_date'    => array ('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('location', __FUNCTION__, $params);
        }
        else {
            $data     = $this->api->internalCall('location', 'data', array ('location_id' => $params['location_id']));
            $ap_id    = (!empty($data['data']['ap_id'])) ? $data['data']['ap_id'] : 0;
            $timezone = $data['data']['timezone'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $table    = $this->getSessionsTableName($start_date, $end_date, $params['location_id'], $ap_id);
            $oDb      = DBComponent::getInstance($table, 'swarmdata');
            $sSQL     = <<<SQL
    SELECT 
       COUNT(DISTINCT ses1.mac_id) as value,
       DATE_FORMAT(convert_tz(time_login,'GMT', '$timezone'), '%Y-%m-%d') AS date,
       DATE_FORMAT(convert_tz(time_login,'GMT', '$timezone'), '%k') AS hour
    FROM $table as ses1
    INNER JOIN mac_address 
            ON ses1.mac_id = mac_address.id
    WHERE status !='noise' 
       AND (         
         sessionid='instore' OR 
         sessionid='passive' OR 
         sessionid='active' OR 
         sessionid='login'
       )     
       AND noise IS false 
       AND time_logout IS NOT NULL 
       AND time_login != time_logout
       AND network_id= $ap_id
       AND time_login BETWEEN '$start_date' AND '$end_date'
    GROUP BY date, hour    
SQL;
            $aRes     = $oDb->fetchAll($sSQL);
            return $this->format($aRes, $data, $params, '/location/' . __FUNCTION__, 0, 0);
        }
    }

    public function devices ($params)
    {
        $rules = array (
            'location_id' => array ('required', 'int'),
            'start_date'  => array ('required', 'date', 'date_interval'),
            'end_date'    => array ('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('location', __FUNCTION__, $params);
        }
        else {
            $data     = $this->api->internalCall('location', 'data', array ('location_id' => $params['location_id']));
            $ap_id    = (!empty($data['data']['ap_id'])) ? $data['data']['ap_id'] : 0;
            $timezone = $data['data']['timezone'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $table    = $this->getSessionsTableName($start_date, $end_date, $params['location_id'], $ap_id);
            $oDb      = DBComponent::getInstance($table, 'swarmdata');
            $sSQL     = <<<SQL
    SELECT 
       COUNT(DISTINCT ses1.mac_id) as value,
       DATE_FORMAT(convert_tz(time_login,'GMT', '$timezone'), '%Y-%m-%d') AS date,
       DATE_FORMAT(convert_tz(time_login,'GMT', '$timezone'), '%k') AS hour
    FROM $table as ses1
    INNER JOIN mac_address 
            ON ses1.mac_id = mac_address.id
    WHERE status !='noise' 
       AND (         
         sessionid='passerby' OR 
         sessionid='instore' OR 
         sessionid='passive' OR 
         sessionid='active' OR 
         sessionid='login'
       )     
       AND noise IS false 
       AND time_logout IS NOT NULL         
       AND time_login != time_logout
       AND network_id= $ap_id
       AND time_login BETWEEN '$start_date' AND '$end_date'
    GROUP BY date, hour    
SQL;
            $aRes     = $oDb->fetchAll($sSQL);
            return $this->format($aRes, $data, $params, '/location/' . __FUNCTION__, 0, 0);
        }
    }

    //Rates
    public function itemsPerTransaction ($params)
    {
        $tt                = $this->api->internalCall('location', 'totalItems', $params);
        $tr                = $this->api->internalCall('location', 'transactions', $params);
        $result            = $this->calculate($tt, $tr, true);
        $result['options'] = array (
            'endpoint'    => '/location/' . __FUNCTION__,
            'location_id' => $params['location_id'],
            'start_date'  => $params['start_date'],
            'end_date'    => $params['end_date'],
        );
        return $result;
    }

    public function windowConversion ($params)
    {
        $ft                = $this->api->internalCall('location', 'traffic', $params);
        $nd                = $this->api->internalCall('location', 'devices', $params);
        $result            = $this->percentify($ft, $nd);
        $result['options'] = array (
            'endpoint'    => '/location/' . __FUNCTION__,
            'location_id' => $params['location_id'],
            'start_date'  => $params['start_date'],
            'end_date'    => $params['end_date'],
        );
        return $result;
    }

    public function avgTicket ($params)
    {
        $re                = $this->api->internalCall('location', 'revenue', $params);
        $tr                = $this->api->internalCall('location', 'transactions', $params);
        $result            = $this->calculate($re, $tr, true);
        $result['options'] = array (
            'endpoint'    => '/location/' . __FUNCTION__,
            'location_id' => $params['location_id'],
            'start_date'  => $params['start_date'],
            'end_date'    => $params['end_date'],
        );
        return $result;
    }

    public function conversionRate ($params)
    {
        $rules = array (
            'location_id' => array ('required', 'int'),
            'start_date'  => array ('required', 'date', 'date_interval'),
            'end_date'    => array ('required', 'date')
        );
        $this->validate($params, $rules);
        if ($params['start_date'] != $params['end_date']) {
            return $this->iterativeCall('location', __FUNCTION__, $params);
        }
        else {
            $aDevices = getDeviceTypesInLocation($params['location_id']);
            $data     = $this->api->internalCall('location', 'data', array ('location_id' => $params['location_id']));
            $default  = $data['data']['conversionRate_default_device'];
            $default  = (empty($default)) ? 'portal' : $default;
            $timezone = $data['data']['timezone'];
            list($start_date, $end_date, $timezone) = $this->parseDates($params, $timezone);
            $firstSensor = firstSensor($params['location_id']);
            $sdate = new DateTime($firstSensor, new DateTimeZone($timezone));
            $cdate = new DateTime($start_date, new DateTimeZone($timezone));
            if (in_array('portal', $aDevices) && $sdate < $cdate && $default == 'portal') {
                $result = $this->portalConversionRate($params);
                $result['options']['device'] = 'portal';
            }
            else {
                $result = $this->presenceConversionRate($params);
                $result['options']['device'] = 'presence';
            }
            $result['options']['endpoint'] = '/location/' . __FUNCTION__;
            $result['data']['breakdown'][$params['start_date']]['totals']['device'] = $result['options']['device'];
            return $result;
        }
    }

    public function presenceConversionRate ($params)
    {
        $rules = array (
            'location_id' => array ('required', 'int'),
            'start_date'  => array ('required', 'date', 'date_interval'),
            'end_date'    => array ('required', 'date')
        );
        $this->validate($params, $rules);

        $tr     = $this->api->internalCall('location', 'transactions', $params);
        $ft     = $this->api->internalCall('location', 'presenceTraffic', $params);
        $result = $this->percentify($tr, $ft);

        $result['options'] = array (
            'endpoint'    => '/location/' . __FUNCTION__,
            'location_id' => $params['location_id'],
            'start_date'  => $params['start_date'],
            'end_date'    => $params['end_date'],
        );
        return $result;
    }

    /**
     * Get portal conversion rate
     * 
     * @param $params Array containing location_id, start_date and end_date
     * @return array Array of results formatted for display in the dashboard
     */
    public function portalConversionRate ($params)
    {
        $rules = array (
            'location_id' => array ('required', 'int'),
            'start_date'  => array ('required', 'date', 'date_interval'),
            'end_date'    => array ('required', 'date')
        );
        $this->validate($params, $rules);

        $tr     = $this->api->internalCall('location', 'transactions', $params);
        $ft     = $this->api->internalCall('location', 'portalTraffic', $params);
        $result = $this->percentify($tr, $ft);

        $result['options'] = array (
            'endpoint'    => '/location/' . __FUNCTION__,
            'location_id' => $params['location_id'],
            'start_date'  => $params['start_date'],
            'end_date'    => $params['end_date'],
        );
        return $result;
    }

    public function dwell ($params)
    {
        $ts                = $this->api->internalCall('location', 'timeInShop', $params);
        $tr                = $this->api->internalCall('location', 'traffic', $params);
        $result            = $this->calculate($ts, $tr, true);
        $result['options'] = array (
            'endpoint'    => '/location/' . __FUNCTION__,
            'location_id' => $params['location_id'],
            'start_date'  => $params['start_date'],
            'end_date'    => $params['end_date'],
        );
        return $result;
    }

    /**
     * Get location settings
     *
     * @param Array
     */
    public function getSettings ($params)
    {
        if (empty($params['location_id'])) {
            throw new APIException(400, 'bad_request', 'A valid locationId is needed to fetch settings.');
        }
        if (empty($params['uuid'])) {
            throw new APIException(400, 'bad_request', 'User not found. Please provide a valid UUID.');
        }
        $this->verify($params);
        $location_id = $params['location_id'];
        $location    = new Location();
        if (!$location->locationExists($location_id)) {
            throw new APIException(400, 'bad_request', 'Location not found.');
        }

        $ret['data'] = array ();
        $oDb         = DBComponent::getInstance('location_setting', 'backstage');
        $sSQL        = <<<SQL
SELECT name
    FROM location
    WHERE id=$location_id
SQL;

        $aResL = $oDb->fetchAll($sSQL);
        if (!empty($aResL)) {
            $ret['data']['name'] = $aResL[0]['location']['name'];
        }
        $sSQL                    = <<<SQL
SELECT s.label, s.name, s.desc,ls.setting_id, ls.value
    FROM setting s JOIN location_setting ls on s.id=ls.setting_id
    WHERE ls.location_id=$location_id
SQL;
        $aRes                    = $oDb->fetchAll($sSQL);
        $ret['data']['settings'] = array ();
        $defaults                = $this->getDefaultSettings();
        if (!empty($aRes)) {
            foreach ($aRes as $set) {
                $ret['data']['settings'][$set['s']['name']] = array (
                    'label'       => $set['s']['label'],
                    'setting_id'  => $set['ls']['setting_id'],
                    'value'       => $set['ls']['value'],
                    'description' => $set['s']['desc']
                );
            }

            foreach ($defaults as $name => $s) {
                if (empty($ret['data']['settings'][$name])) {
                    $ret['data']['settings'][$name] = array (
                        'label'       => $s['label'],
                        'id'          => $s['id'],
                        'value'       => $s['value'],
                        'description' => $s['desc']
                    );
                }
            }
        }
        else {
            $ret['data']['settings'] = $defaults;
        }
        $ret['options'] = array (
            'endpoint'    => '/location/' . __FUNCTION__,
            'location_id' => $location_id
        );
        return $ret;
    }

    /**
     * Update location settings
     *
     * @param Array
     */
    public function updateSettings ($params)
    {
        if (empty($params['location_id'])) {
            throw new APIException(400, 'bad_request', 'A valid locationId is needed to fetch settings.');
        }
        if (empty($params['uuid'])) {
            throw new APIException(400, 'bad_request', 'User not found. Please provide a valid UUID.');
        }
        $this->verify($params);
        $location_id = $params['location_id'];
        $oSettings   = new Setting();
        $settingIds  = $oSettings->find('list', array ('fields' => array ('id')));
        $update      = array ();
        if (!empty($params['Location'][$location_id])) {
            if (!empty($params['Location']['name'])) {
                $sSQL = <<<SQL
UPDATE location 
    SET name = :name
    WHERE location_id = :location_id
SQL;
                $oDb->query($sSQL, array (
                    'name'        => $params['Location']['name'],
                    'location_id' => $location_id,
                ));
            }

            foreach ($params['Location'][$location_id] as $key => $val) {
                if (!is_numeric($key)) {
                    $sett_id = settId($key);
                }
                else {
                    if (in_array($key, $settingIds)) {
                        $sett_id = $key;
                    }
                }
                if (!empty($sett_id)) {
                    $update[$sett_id] = $val;
                }
            }
            if (!empty($update)) {
                $oDb = DBComponent::getInstance('location_setting', 'backstage');

                foreach ($update as $key => $val) {
                    $sSQL = <<<SQL
INSERT INTO location_setting 
    SET location_id = :location_id,
        setting_id  = :setting_id,
        value = :value
    ON DUPLICATE KEY UPDATE value = :value
SQL;
                    $ret  = $oDb->query($sSQL, array (
                        'location_id' => $location_id,
                        'setting_id'  => $key,
                        'value'       => $val
                    ));
                }
                return array (
                    'options' => array (
                        'endpoint'    => '/location/' . __FUNCTION__,
                        'uuid'        => $params['uuid'],
                        'location_id' => $params['uuid']
                    ),
                    'message' => array (
                        'success' => 'Settings have been successfully saved.'
                    )
                );
            }
        }
        else {
            return array (
                'options' => array (
                    'endpoint' => '/location/' . __FUNCTION__,
                ),
                'message' => array (
                    'success' => 'Nothing to update.'
                )
            );
        }
    }

    /**
     * Create a new location
     * 
     * @param Array post data
     */
    public function create ($params)
    {
        $uuid     = '';
        $user_id  = NULL;
        $user     = array ();
        $location = new Location();
        $location->set($params);
        if (empty($params['uuid'])) {
            throw new APIException(400, 'bad_request', 'User not found. Please provide a valid UUID.');
        }

        if (!$location->validates()) {
            $this->handleValidationErrors($location->validationErrors);
        }

        $combination = $params['address1'] . " " . $params['city'];
        if ($location->nameAddressCombination($combination, $params['name']) > 0) {
            throw new APIException(400, 'bad_request', 'Location name, address and city combination already exists in our records.');
        }

        $params['country'] = strtoupper($params['country']);
        if (!$location->countryCodeExists($params['country'])) {
            throw new APIException(400, 'bad_request', 'Country code does not exist in our database.');
        }
        $uuid = $params['uuid'];
        if (empty($params['user_id'])) {
            $user    = $this->getUserFromUUID($uuid);
            $user_id = $user[0]['user']['id'];
        }

        $locationmanager_id = $this->getLocationManagerId($user_id);

        $oDb         = DBComponent::getInstance('location_setting', 'backstage');
        $reseller_id = (!empty($params['reseller_id'])) ? $params['reseller_id'] : NULL;

        // Create a new Location
        $sSQL        = <<<SQL
INSERT INTO location
    SET name = :name,
        ts_creation = CURRENT_TIMESTAMP,
        reseller_id = :reseller_id
SQL;
        $oDb->query($sSQL, array (
            ':name'        => $params['name'],
            ':reseller_id' => $reseller_id,
        ));
        $location_id = $oDb->lastInsertId();

        // Add addresss location settings
        foreach (['address1', 'address2', 'city', 'state', 'country', 'zipcode'] as $key) {
            if (array_key_exists($key, $params)) {
                $sett_id = settId($key);
                $sSQL    = <<<SQL
INSERT INTO location_setting 
    SET location_id = :location_id,
        setting_id  = :setting_id,
        value = :value
    ON DUPLICATE KEY UPDATE value = :value
SQL;
                $oDb->query($sSQL, array (
                    ':location_id' => $location_id,
                    ':setting_id'  => $sett_id,
                    ':value'       => $params[$key],
                ));
            }
        }
        // Create locationmanager to location map
        $sSQL = <<<SQL
INSERT INTO locationmanager_location
    SET location_id = :location_id,
        locationmanager_id = :locationmanager_id
SQL;

        $oDb->query($sSQL, array (
            'location_id'        => $location_id,
            'locationmanager_id' => $locationmanager_id
        ));

        $oLocation = new Location();
        $oLocation = $oLocation->find('first', ['conditions' => ['Location.id' => $location_id]]);

        $address1 = settVal('address1', $oLocation['Setting']);
        $address2 = settVal('address2', $oLocation['Setting']);
        $city     = settVal('city', $oLocation['Setting']);
        $state    = settVal('state', $oLocation['Setting']);
        $country  = settVal('country', $oLocation['Setting']);
        $zipcode  = settVal('zipcode', $oLocation['Setting']);

        $subject = "Location #" . $location_id . ' ( ' . $oLocation['Location']['name'] . ' ) was added from API';
        $msg     = <<<TEXT
<div>
    Location $location_id was just added/modified on the
    MDM using the API with the following info:
</div>
<ul>
    <li>Location Name: {$oLocation['Location']['name']}</li>
    <li>Reseller: {$oLocation['Reseller']['name']}</li>
    <li>Address 1: $address1</li>
    <li>Address 2: $address2</li>
    <li>City: $city</li>
    <li>State: $state</li>
    <li>Country: $country</li>
    <li>Zip: $zipcode</li>
</ul>
TEXT;
        EmailQueueComponent::queueEmail(
                'info@swarm-mobile.com', 'Info', 'am@swarm-mobile.com', 'AM', $subject, $msg
        );

        return array (
            'data'    => array (
                'user_id'            => $user_id,
                'locationmanager_id' => $locationmanager_id,
                'location_id'        => $location_id
            ),
            'options' => array (
                'endpoint' => '/location/' . __FUNCTION__,
                'uuid'     => $uuid
            ),
            'message' => array (
                'success' => 'Location has been successfully created.'
            )
        );
    }

    /**
     * Get available settings 
     * 
     */
    public function availableSettings ($params)
    {
        if (empty($params['uuid'])) {
            throw new APIException(400, 'bad_request', 'User not found. Please provide a valid UUID.');
        }
        $oDb                     = DBComponent::getInstance('user', 'backstage');
        $sSQL                    = <<<SQL
SELECT `id`, `label`, `name`, `default`, `desc`
    FROM setting
SQL;
        $settings                = $oDb->fetchAll($sSQL);
        $ret['data']['settings'] = array ();
        foreach ($settings as $set) {
            $ret['data']['settings'][$set['setting']['name']] = array (
                'id'      => $set['setting']['id'],
                'label'   => $set['setting']['label'],
                'desc'    => $set['setting']['desc'],
                'default' => $set['setting']['default']
            );
        }
        return $ret;
    }

    public function data ($params)
    {
        $rules       = array ('location_id' => array ('required', 'int'));
        $this->validate($params, $rules);
        $location_id = $params['location_id'];
        $oLocation   = new Location();
        $oLocation   = $oLocation->find('first', ['conditions' => ['Location.id' => $location_id]]);
        $aFields     = [
            'ap_id'                     => 'network_id',
            'timezone'                  => 'timezone',
            'location_open'             => 'location_open',
            'location_close'            => 'location_close',
            'lightspeed_id'             => 'pos_store_id',
            'monday_open'               => 'monday_open',
            'monday_close'              => 'monday_close',
            'tuesday_open'              => 'tuesday_open',
            'tuesday_close'             => 'tuesday_close',
            'wednesday_open'            => 'wednesday_open',
            'wednesday_close'           => 'wednesday_close',
            'thursday_open'             => 'thursday_open',
            'thursday_close'            => 'thursday_close',
            'friday_open'               => 'friday_open',
            'friday_close'              => 'friday_close',
            'saturday_open'             => 'saturday_open',
            'saturday_close'            => 'saturday_close',
            'sunday_open'               => 'sunday_open',
            'sunday_close'              => 'sunday_close',
            'network_provider'          => 'network_provider',
            'register_filter'           => 'register_filter',
            'outlet_filter'             => 'outlet_filter',
            'country'                   => 'country',
            'nightclub_hours'           => 'nightclub_hours',
            'traffic_factor'            => 'traffic_factor',
            'no_rollups'                => 'no_rollups',
            'no_cache'                  => 'no_cache',
            'nightclub_hours_location'  => 'nightclub_hours_location',
            'transactions_while_closed' => 'transactions_while_closed',
            'footTraffic_default_device' => 'footTraffic_default_device',
            'conversionRate_default_device' => 'conversionRate_default_device',
        ];
        $tmp         = array ('data' => array ());
        foreach ($aFields as $kOld => $kNew) {
            $tmp['data'][$kOld] = settVal($kNew, $oLocation['Setting']);
        }
        if (empty($tmp['data']['network_provider'])) {
            $tmp['data']['network_provider'] = 'gp';
        }
        if (empty($tmp['data']['timezone'])) {
            $tmp['data']['timezone'] = 'America/Los_Angeles';
        }
        if (empty($tmp['data']['footTraffic_default_device'])) {
            $tmp['data']['footTraffic_default_device'] = 'portal';
        }
        if (empty($tmp['data']['conversionRate_default_device'])) {
            $tmp['data']['conversionRate_default_device'] = 'portal';
        }
        foreach (array ('open', 'close') as $state) {
            if (empty($tmp['data']['location_' . $state])) {
                $tmp['data']['location_' . $state] = $state == 'open' ? '09:00' : '21:00';
            }
            foreach ($this->weekdays as $day) {
                $daystate = $day . '_' . $state;
                $val      = isset($tmp['data'][$daystate]) ? $tmp['data'][$daystate] : null;
                if (is_null($val) || trim($val) === '') {
                    $tmp['data'][$daystate] = $tmp['data']['location_' . $state];
                }
            }
        }
        return $tmp;
    }

    /**
     * Verify if the the user has access to the location
     * 
     * @param Array 
     */
    private function verify ($params)
    {
        $rules       = array ('location_id' => array ('required', 'int'), 'uuid' => array ('required'));
        $this->validate($params, $rules);
        $location_id = $params['location_id'];
        $uuid        = $params['uuid'];

        $aRes = $this->getUserFromUUID($uuid);
        if (empty($aRes))
            throw new APIException(401, 'authentication_failed', 'Supplied credentials are invalid');

        $user_id = $aRes[0]['user']['id'];
        $oDb     = DBComponent::getInstance('location', 'backstage');
        // Check for location and user map
        switch ($aRes[0]['user']['usertype_id']) {
            case 1:
                return true;
                break;
            case 2:
                return true;
                break;
            case 4:
                $sSQL      = <<<SQL
SELECT  lml.location_id
    FROM locationmanager l JOIN locationmanager_location lml
    ON l.id=lml.locationmanager_id
    WHERE l.user_id="$user_id"
SQL;
                $locations = $oDb->fetchAll($sSQL);
                break;
            case 5:
                $sSQL      = <<<SQL
SELECT  le.location_id
    FROM employee e JOIN location_employee le
    ON e.id=le.employee_id
    WHERE e.user_id="$user_id"
SQL;
                $locations = $oDb->fetchAll($sSQL);
                break;
        }
        foreach ($locations as $loc) {
            if (!empty($loc['lml']['location_id']) && $loc['lml']['location_id'] == $location_id) {
                return true;
            }
        }

        throw new APIException(400, 'bad_request', 'User not associated to the location provided.');
    }

    private function getLocationManagerId ($user_id)
    {
        if (empty($user_id))
            return false;
        $oDb     = DBComponent::getInstance('user', 'backstage');
        $sSQL    = <<<SQL
SELECT id
    FROM locationmanager
    WHERE user_id="$user_id" LIMIT 1
SQL;
        $manager = $oDb->fetchAll($sSQL);
        return $manager[0]['locationmanager']['id'];
    }

}
