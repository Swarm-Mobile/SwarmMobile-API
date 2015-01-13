<?php

App::uses('Revenue', 'Model/Metrics');
App::uses('Transactions', 'Model/Metrics');
App::uses('PortalTraffic', 'Model/Metrics');
App::uses('MetricModel', 'Model');

class HistoricalTotals extends MetricModel
{

    public $useTable = false;

    public function __construct ($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->setValidationFields(['location_id']);
    }

    public function create ($data = [], $filterKey = false)
    {
        $possibleLocationIds            = array_filter([
            (isset($data['location_id'])) ? $data['location_id'] : null,
            (isset($data[__CLASS__]['location_id'])) ? $data[__CLASS__]['location_id'] : null,
        ]);
        $data[__CLASS__]['location_id'] = array_shift($possibleLocationIds);
        $data[__CLASS__]['start_date']  = '2013-01-01';
        $data[__CLASS__]['end_date']    = '2013-01-01';
        parent::create($data, $filterKey);
    }

    public function getFromRaw ()
    {
        if (!$this->validates()) {
            throw new Swarm\ApplicationErrorException(SwarmErrorCodes::MODEL_NOT_INITIALIZED);
        }
        $result             = [
            'data'    => ['totals' => MetricFormatComponent::emptyHistoricalTotals()],
            'options' => [
                'endpoint'    => "/location/historicalTotals",
                'location_id' => $this->getLocationId()
            ]
        ];
        $locationSetting    = $this->getLocationSetting();
        $whileClosed        = $locationSetting->getSettingValue(LocationSetting::TRANSACTIONS_WHILE_CLOSED);
        $openTotal          = $whileClosed == 'no' ? 'open' : 'total';
        $openHours          = $locationSetting->getOpenHours();
        $defaultFootTraffic = $locationSetting->getSettingValue(LocationSetting::FOOTTRAFFIC_DEFAULT_DEVICE);
        $dates              = array_filter([
            strtolower($defaultFootTraffic) == 'presence' 
                ? $locationSetting->getFirstSessionDate() 
                : $locationSetting->getFirstSensorDate(),
            $locationSetting->getFirstPurchaseDate(),
        ]);
        $startDate          = array_shift($dates);
        $endDate            = date('Y-m-d');
        if ($startDate != null) {
            $models = [
                'revenue',
                'transactions',
                (
                    strtolower($defaultFootTraffic) == 'presence' 
                        ? 'presenceTraffic' 
                        : 'portalTraffic'
                ),
            ];
            foreach ($models as $cmodel) {
                $model      = $cmodel . 'Model';
                $resultVar  = $cmodel . 'Result';
                $dataVar    = $cmodel . 'Data';
                $classname  = ucfirst($cmodel);
                $$model     = new $classname();
                $$model->create([
                    'location_id' => $this->getLocationId(),
                    'start_date'  => $startDate,
                    'end_date'    => $endDate
                        ], true);
                $$resultVar = $$model->getFromCache();
                $$dataVar   = MetricFormatComponent::formatAsSum($startDate, $endDate, $$resultVar, $openHours);
            }
            $trafficData = ($defaultFootTraffic == 'Presence' ? $presenceTrafficData : $portalTrafficData);
            $days        = ['byWeek' => [], 'total' => 0];
            $weeks       = [];
            $end         = new DateTime($endDate);
            $start       = new DateTime($startDate);
            $cMonth      = date_format($start, 'm');
            $tMonth      = $cMonth;
            $nMonths     = 1;
            while ($start <= $end) {
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
            $r = &$result['data']['totals'];

            $r['revenue']        = $revenueData['totals'][$openTotal];
            $r['transactions']   = $transactionsData['totals'][$openTotal];
            $r['visitors']       = $trafficData['totals']['open'];
            $r['conversionRate'] = MetricFormatComponent::rate($r['transactions'], $r['visitors']);

            $r['avgTransactionsDaily']   = MetricFormatComponent::ratio($r['transactions'], $days['total']);
            $r['avgTransactionsWeekly']  = MetricFormatComponent::ratio($r['transactions'], count($days['byWeek']));
            $r['avgTransactionsMonthly'] = MetricFormatComponent::ratio($r['transactions'], $nMonths);

            $r['avgRevenueDaily']   = MetricFormatComponent::ratio($r['revenue'], $days['total']);
            $r['avgRevenueWeekly']  = MetricFormatComponent::ratio($r['revenue'], count($days['byWeek']));
            $r['avgRevenueMonthly'] = MetricFormatComponent::ratio($r['revenue'], $nMonths);

            $r['avgVisitorsDaily']   = MetricFormatComponent::ratio($r['visitors'], $days['total']);
            $r['avgVisitorsWeekly']  = MetricFormatComponent::ratio($r['visitors'], count($days['byWeek']));
            $r['avgVisitorsMonthly'] = MetricFormatComponent::ratio($r['visitors'], $nMonths);

            $r['avgConversionRateDaily']   = $r['conversionRate'];
            $r['avgConversionRateWeekly']  = MetricFormatComponent::rate($r['avgTransactionsWeekly'], $r['avgVisitorsWeekly']);
            $r['avgConversionRateMonthly'] = MetricFormatComponent::rate($r['avgTransactionsMonthly'], $r['avgVisitorsMonthly']);

            $r['conversionRate'] = $r['avgConversionRateDaily'];
        }
        return $result;
    }

}
