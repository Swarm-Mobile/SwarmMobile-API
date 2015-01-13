<?php

App::uses('Revenue', 'Model/Metrics');
App::uses('Transactions', 'Model/Metrics');
App::uses('PortalTraffic', 'Model/Metrics');
App::uses('MetricModel', 'Model');

class MonthlyTotals extends MetricModel
{

    public $useTable = false;
    protected $month;
    protected $year;

    public function __construct ($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->setValidationFields(['location_id', 'month', 'year']);
    }

    public function isMonth ($month, $arg2 = 0)
    {
        return in_array($month['month'], [
            '01', '02', '03', '04', '05', '06',
            '07', '08', '09', '10', '11', '12'
        ]);
    }

    public function isYear ($year, $arg2 = 0)
    {
        $year = $year['year'];
        return is_numeric($year) && $year >= 2012 && $year <= (date('Y') + 1);
    }

    public function getMonth ()
    {
        return $this->month;
    }

    public function getYear ()
    {
        return $this->year;
    }

    public function setMonth ($month)
    {
        $this->month = $month;
        return $this;
    }

    public function setYear ($year)
    {
        $this->year = $year;
        return $this;
    }

    public function create ($data = [], $filterKey = false)
    {
        $this->set($data);
        if ($this->validates()) {
            $month       = $this->data[__CLASS__]['month'];
            $year        = $this->data[__CLASS__]['year'];
            $startDate   = $year . '-' . $month . '-01';
            $endDate     = ($month === '12') ? (($year + 1) . '-01-01') : ($year . '-' . ($month + 1) . '-01');
            $end         = new DateTime($endDate);
            date_sub($end, date_interval_create_from_date_string('1 days'));
            $endDate     = date_format($end, 'Y-m-d');
            $this->month = $month;
            $this->year  = $year;

            $this->locationId      = $this->data[__CLASS__]['location_id'];
            $this->startDate       = $startDate;
            $this->endDate         = $endDate;
            $this->locationSetting = new LocationSetting();
            $this->locationSetting->create(['location_id' => $this->locationId], true);
            $this->timezone        = $this->locationSetting->getSettingValue(LocationSetting::TIMEZONE);
            $this->startTime       = TimeComponent::convertTimeToGMT($startDate . ' 00:00:00', $this->timezone);
            $this->endTime         = TimeComponent::convertTimeToGMT($endDate . ' 23:59:59', $this->timezone);
        }
    }

    public function getFromRaw ()
    {
        if (!$this->validates()) {
            throw new Swarm\ApplicationErrorException(SwarmErrorCodes::MODEL_NOT_INITIALIZED);
        }
        $locationSetting    = $this->getLocationSetting();
        $defaultFootTraffic = $locationSetting->getSettingValue(LocationSetting::FOOTTRAFFIC_DEFAULT_DEVICE);
        $models             = [
            'revenue',
            'transactions',
            (strtolower($defaultFootTraffic) == 'presence' ? 'presenceTraffic' : 'portalTraffic'),
        ];
        foreach ($models as $cmodel) {
            $model      = $cmodel . 'Model';
            $resultVar  = $cmodel . 'Result';
            $classname  = ucfirst($cmodel);
            $$model     = new $classname();
            $$model->create([
                'location_id' => $this->getLocationId(),
                'start_date'  => $this->getStartDate(),
                'end_date'    => $this->getEndDate()
                    ], true);
            $$resultVar = $$model->getFromCache();
        }
        $trafficResult = strtolower($defaultFootTraffic) == 'presence' ? $presenceTrafficResult : $portalTrafficResult;
        $whileClosed   = $locationSetting->getSettingValue(LocationSetting::TRANSACTIONS_WHILE_CLOSED);
        $openTotal     = $whileClosed == 'no' ? 'open' : 'total';

        $start  = new DateTime($this->getStartDate());
        $end    = new DateTime($this->getEndDate());
        $days   = ['byWeek' => [], 'total' => 0];
        $weeks  = [];
        $result = [
            'data'    => [
                'breakdown' => [],
                'totals'    => [
                    'revenue'        => 0,
                    'visitors'       => 0,
                    'conversionRate' => 0
                ]
            ],
            'options' => [
                'endpoint'    => '/location/monthlyTotals',
                'year'        => $this->getYear(),
                'month'       => $this->getMonth(),
                'location_id' => $this->getLocationId()
            ]
        ];

        while ($start <= $end) {
            $startYMD = date_format($start, 'Y-m-d');
            $endYMD   = date_format($start, 'Y-m-d');
            $startW   = date_format($start, 'W');
            $days['total'] ++;
            if (!isset($days['byWeek'][$startW])) {
                $days['byWeek'][$startW]              = 0;
                $weeks[$startW]['start']              = $startYMD;
                $result['data']['breakdown'][$startW] = [
                    'revenue'                => 0,
                    'visitors'               => 0,
                    'conversionRate'         => 0,
                    'avgRevenueDaily'        => 0,
                    'avgVisitorsDaily'       => 0,
                    'avgConversionRateDaily' => 0
                ];
            }
            $days['byWeek'][$startW] ++;
            if (isset($revenueResult[$startYMD])) {
                $result['data']['breakdown'][$startW]['revenue'] += $revenueResult[$startYMD]['total_' . $openTotal];
                $result['data']['totals']['revenue'] += $revenueResult[$startYMD]['total_' . $openTotal];
            }
            if (isset($trafficResult[$startYMD])) {
                $result['data']['breakdown'][$startW]['visitors'] += $trafficResult[$startYMD]['total_open'];
                $result['data']['totals']['visitors'] += $trafficResult[$startYMD]['total_open'];
            }
            if (isset($transactionsResult[$startYMD])) {
                $result['data']['breakdown'][$startW]['conversionRate'] += $transactionsResult[$startYMD]['total_' . $openTotal];
                $result['data']['totals']['conversionRate'] += $transactionsResult[$startYMD]['total_' . $openTotal];
            }
            $weeks[$startW]['end'] = $endYMD;
            date_add($start, date_interval_create_from_date_string('1 days'));
        }

        foreach ($days['byWeek'] as $w => $c) {
            $r                     = &$result['data']['breakdown'][$w];
            $r['start_date']       = $weeks[$w]['start'];
            $r['end_date']         = $weeks[$w]['end'];
            $r['avgRevenueDaily']  = MetricFormatComponent::ratio($r['revenue'], $c);
            $r['avgVisitorsDaily'] = MetricFormatComponent::ratio($r['visitors'], $c);

            $nonZeroCandiates            = array_filter([$c, 1]);
            $r['avgConversionRateDaily'] = $r['conversionRate'] / array_shift($nonZeroCandiates);
            $r['avgConversionRateDaily'] = MetricFormatComponent::rate($r['avgConversionRateDaily'], $r['avgVisitorsDaily']);
            $r['conversionRate']         = $r['avgConversionRateDaily'];
        }
        $t                           = &$result['data']['totals'];
        $t['conversionRate']         = MetricFormatComponent::rate($t['conversionRate'], $t['visitors']);
        $t['avgRevenueDaily']        = MetricFormatComponent::ratio($t['revenue'], $days['total']);
        $t['avgVisitorsDaily']       = MetricFormatComponent::ratio($t['visitors'], $days['total']);
        $t['avgConversionRateDaily'] = MetricFormatComponent::ratio($t['conversionRate'], $days['total']);

        return $result;
    }

}
