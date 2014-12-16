<?php

App::uses('Model', 'Model');
App::uses('MetricModel', 'Model/Metrics');
App::uses('LocationSetting', 'Model/Location');
App::uses('MetricFormatComponent', 'Controller/Component');

class MetricController extends AppController
{

    protected $devices;
    protected $portalTraffic;
    protected $presenceReturningByDate;
    protected $presenceReturningByHour;
    protected $presenceTrafficByDate;
    protected $presenceTrafficByHour;
    protected $revenue;
    protected $timeInShop;
    protected $totalItems;
    protected $totals;
    protected $traffic;
    protected $transactions;
    protected $walkbys;
    protected $footTraffic;
    protected $returning;
    protected $monthlyTotals;
    protected $historicalTotals;

    public function getDevices ()
    {
        if (empty($this->devices)) {
            App::uses('Devices', 'Model/Metrics');
            $this->devices = new Devices();
        }
        return $this->devices;
    }

    public function getPortalTraffic ()
    {
        if (empty($this->portalTraffic)) {
            App::uses('PortalTraffic', 'Model/Metrics');
            $this->portalTraffic = new PortalTraffic();
        }
        return $this->portalTraffic;
    }

    public function getPresenceReturningByDate ()
    {
        if (empty($this->presenceReturningByDate)) {
            App::uses('PresenceReturningByDate', 'Model/Metrics');
            $this->presenceReturningByDate = new PresenceReturningByDate();
        }
        return $this->presenceReturningByDate;
    }

    public function getPresenceReturningByHour ()
    {
        if (empty($this->presenceReturningByHour)) {
            App::uses('PresenceReturningByHour', 'Model/Metrics');
            $this->presenceReturningByHour = new PresenceReturningByHour();
        }
        return $this->presenceReturningByHour;
    }

    public function getPresenceTrafficByDate ()
    {
        if (empty($this->presenceTrafficByDate)) {
            App::uses('PresenceTrafficByDate', 'Model/Metrics');
            $this->presenceTrafficByDate = new PresenceTrafficByDate();
        }
        return $this->presenceTrafficByDate;
    }

    public function getPresenceTrafficByHour ()
    {
        if (empty($this->presenceTrafficByHour)) {
            App::uses('PresenceTrafficByHour', 'Model/Metrics');
            $this->presenceTrafficByHour = new PresenceTrafficByHour();
        }
        return $this->presenceTrafficByHour;
    }

    public function getRevenue ()
    {
        if (empty($this->revenue)) {
            App::uses('Revenue', 'Model/Metrics');
            $this->revenue = new Revenue();
        }
        return $this->revenue;
    }

    public function getTimeInShop ()
    {
        if (empty($this->timeInShop)) {
            App::uses('TimeInShop', 'Model/Metrics');
            $this->timeInShop = new TimeInShop();
        }
        return $this->timeInShop;
    }

    public function getTotalItems ()
    {
        if (empty($this->totalItems)) {
            App::uses('TotalItems', 'Model/Metrics');
            $this->totalItems = new TotalItems();
        }
        return $this->totalItems;
    }

    public function getTotals ()
    {
        if (empty($this->totals)) {
            App::uses('Totals', 'Model/Totals');
            $this->totals = new Totals();
        }
        return $this->totals;
    }

    public function getTraffic ()
    {
        if (empty($this->traffic)) {
            App::uses('Traffic', 'Model/Metrics');
            $this->traffic = new Traffic();
        }
        return $this->traffic;
    }

    public function getTransactions ()
    {
        if (empty($this->transactions)) {
            App::uses('Transactions', 'Model/Metrics');
            $this->transactions = new Transactions();
        }
        return $this->transactions;
    }

    public function getWalkbys ()
    {
        if (empty($this->walkbys)) {
            App::uses('Walkbys', 'Model/Metrics');
            $this->walkbys = new Walkbys();
        }
        return $this->walkbys;
    }

    public function getFootTraffic ($data = null)
    {
        if (empty($this->footTraffic)) {
            $device = 'portal';
            if (isset($data['location_id'])) {
                $locationSetting = new LocationSetting();
                $locationSetting->setLocationId($data['location_id']);
                $device          = $locationSetting->getSettingValue(LocationSetting::FOOTTRAFFIC_DEFAULT_DEVICE);
                $device          = (empty($device)) ? 'portal' : $device;
            }
            $class             = ucfirst($device) . 'Traffic';
            App::uses($class, 'Model/Metrics');
            $this->footTraffic = new $class();
        }
        return $this->footTraffic;
    }

    public function getReturning ()
    {
        if (empty($this->returning)) {
            App::uses('PresenceReturning', 'Model/Metrics');
            $this->returning = new PresenceReturning();
        }
        return $this->returning;
    }

    public function getMonthlyTotals ()
    {
        if (empty($this->monthlyTotals)) {
            App::uses('MonthlyTotals', 'Model/Totals');
            $this->monthlyTotals = new MonthlyTotals();
        }
        return $this->monthlyTotals;
    }

    public function getHistoricalTotals ()
    {
        if (empty($this->historicalTotals)) {
            App::uses('HistoricalTotals', 'Model/Totals');
            $this->historicalTotals = new HistoricalTotals();
        }
        return $this->historicalTotals;
    }

    public function setDevices (Devices $devices)
    {
        $this->devices = $devices;
        return $this;
    }

    public function setPortalTraffic (PortalTraffic $portalTraffic)
    {
        $this->portalTraffic = $portalTraffic;
        return $this;
    }

    public function setPresenceReturningByDate (PresenceReturningByDate $presenceReturningByDate)
    {
        $this->presenceReturningByDate = $presenceReturningByDate;
        return $this;
    }

    public function setPresenceReturningByHour (PresenceReturningByHour $presenceReturningByHour)
    {
        $this->presenceReturningByHour = $presenceReturningByHour;
        return $this;
    }

    public function setPresenceTrafficByDate (PresenceTrafficByDate $presenceTrafficByDate)
    {
        $this->presenceTrafficByDate = $presenceTrafficByDate;
        return $this;
    }

    public function setPresenceTrafficByHour (PresenceTrafficByHour $presenceTrafficByHour)
    {
        $this->presenceTrafficByHour = $presenceTrafficByHour;
        return $this;
    }

    public function setRevenue (Revenue $revenue)
    {
        $this->revenue = $revenue;
        return $this;
    }

    public function setTimeInShop (TimeInShop $timeInShop)
    {
        $this->timeInShop = $timeInShop;
        return $this;
    }

    public function setTotalItems (TotalItems $totalItems)
    {
        $this->totalItems = $totalItems;
        return $this;
    }

    public function setTotals (Totals $totals)
    {
        $this->totals = $totals;
        return $this;
    }

    public function setTraffic (Traffic $traffic)
    {
        $this->traffic = $traffic;
        return $this;
    }

    public function setTransactions (Transactions $transactions)
    {
        $this->transactions = $transactions;
        return $this;
    }

    public function setWalkbys (Walkbys $walkbys)
    {
        $this->walkbys = $walkbys;
        return $this;
    }

    public function setFootTraffic (FootTraffic $footTraffic)
    {
        $this->footTraffic = $footTraffic;
        return $this;
    }

    public function setReturning (Returning $returning)
    {
        $this->returning = $returning;
        return $this;
    }

    public function setMonthlyTotals (MonthlyTotals $monthlyTotals)
    {
        $this->monthlyTotals = $monthlyTotals;
        return $this;
    }

    public function setHistoricalTotals (HistoricalTotals $historicalTotals)
    {
        $this->historicalTotals = $historicalTotals;
        return $this;
    }

    private function _nightclubHoursSwitch ($data, $locationSetting)
    {
        $nightclubHours = $locationSetting->getSettingValue(LocationSetting::NIGHTCLUB_HOURS);
        if ($nightclubHours === 'yes') {
            $timezone          = $locationSetting->getTimezone();
            $nightclubLocation = $locationSetting->getSettingValue(LocationSetting::NIGHTCLUB_HOURS_LOCATION);
            $data              = MetricFormatComponent::nightclubHoursSwitch($data, $timezone, $nightclubHours, $nightclubLocation);
        }
        return $data;
    }

    private function _formatResult (MetricModel $model, $endpoint, $data)
    {
        return [
            'data'    => $data,
            'options' => [
                'endpoint'    => '/location/' . $endpoint,
                'location_id' => $model->getLocationId(),
                'start_date'  => $model->getStartDate(),
                'end_date'    => $model->getEndDate()
            ]
        ];
    }

    public function avgTicket ()
    {        
        $transactions = $this->getTransactions();
        $revenue      = $this->getRevenue();        
        $transactions->create($this->request->query, true);
        $revenue->create($this->request->query, true);        
        if ($transactions->validates() && $revenue->validates()) {
            $locationSetting       = $transactions->getLocationSetting();
            $transactionsResultset = $transactions->getFromCache();
            $revenueResultset      = $revenue->getFromCache();            
            $data = MetricFormatComponent::formatAsRatio(
                $transactions->getStartDate(), 
                $transactions->getEndDate(), 
                $revenueResultset, 
                $transactionsResultset, 
                $locationSetting->getOpenHours()
            );
            $data = $this->_nightclubHoursSwitch($data, $locationSetting);
            return new JsonResponse(['body' => $this->_formatResult($transactions, __FUNCTION__, $data)]);
        }
        throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($transactions->validationErrors));        
    }

    public function conversionRate ()
    {
        $transactions = $this->getTransactions();
        $footTraffic  = $this->getFootTraffic($this->request->query);
        $transactions->create($this->request->query, true);
        $footTraffic->create($this->request->query, true);
        if ($transactions->validates() && $footTraffic->validates()) {
            $locationSetting       = $transactions->getLocationSetting();
            $transactionsResultset = $transactions->getFromCache();
            $footTrafficResultset  = $footTraffic->getFromCache();

            $data = MetricFormatComponent::formatAsRate(
                $transactions->getStartDate(), 
                $transactions->getEndDate(), 
                $transactionsResultset, 
                $footTrafficResultset, 
                $locationSetting->getOpenHours()
            );
            $data = $this->_nightclubHoursSwitch($data, $locationSetting);
            return new JsonResponse(['body' => $this->_formatResult($transactions, __FUNCTION__, $data)]);
        }
        throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($transactions->validationErrors));
    }

    public function dwell ()
    {
        $traffic    = $this->getTraffic();
        $timeInShop = $this->getTimeInShop();
        $traffic->create($this->request->query, true);
        $timeInShop->create($this->request->query, true);
        if ($traffic->validates() && $timeInShop->validates()) {
            $locationSetting     = $traffic->getLocationSetting();
            $trafficResultset    = $traffic->getFromCache();
            $timeInShopResultset = $timeInShop->getFromCache();

            $data = MetricFormatComponent::formatAsRatio(
                $traffic->getStartDate(), 
                $traffic->getEndDate(), 
                $timeInShopResultset, 
                $trafficResultset, 
                $locationSetting->getOpenHours()
            );
            $data = $this->_nightclubHoursSwitch($data, $locationSetting);
            return new JsonResponse(['body' => $this->_formatResult($traffic, __FUNCTION__, $data)]);
        }
        throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($traffic->validationErrors));
    }

    public function footTraffic ()
    {
        $footTraffic = $this->getFootTraffic($this->request->query);
        $footTraffic->create($this->request->query, true);
        if ($footTraffic->validates()) {
            $locationSetting = $footTraffic->getLocationSetting();
            $resultset       = $footTraffic->getFromCache();

            $data = MetricFormatComponent::formatAsSum(
                $footTraffic->getStartDate(), 
                $footTraffic->getEndDate(), 
                $resultset, 
                $locationSetting->getOpenHours()
            );
            $data = $this->_nightclubHoursSwitch($data, $locationSetting);
            return new JsonResponse(['body' => $this->_formatResult($footTraffic, __FUNCTION__, $data)]);
        }
        throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($footTraffic->validationErrors));
    }

    public function itemsPerTransaction ()
    {
        $transactions = $this->getTransactions();
        $totalItems   = $this->getTotalItems();
        $transactions->create($this->request->query, true);
        $totalItems->create($this->request->query, true);
        if ($transactions->validates() && $totalItems->validates()) {
            $locationSetting       = $transactions->getLocationSetting();
            $transactionsResultset = $transactions->getFromCache();
            $totalItemsResultset   = $totalItems->getFromCache();

            $data = MetricFormatComponent::formatAsRatio(
                $transactions->getStartDate(), 
                $transactions->getEndDate(), 
                $totalItemsResultset, 
                $transactionsResultset, 
                $locationSetting->getOpenHours()
            );
            $data = $this->_nightclubHoursSwitch($data, $locationSetting);
            return new JsonResponse(['body' => $this->_formatResult($transactions, __FUNCTION__, $data)]);
        }
        throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($transactions->validationErrors));
    }

    public function totalItems ()
    {
        $totalItems = $this->getTotalItems();
        $totalItems->create($this->request->query, true);
        if ($totalItems->validates()) {
            $locationSetting = $totalItems->getLocationSetting();
            $resultset       = $totalItems->getFromCache();

            $data = MetricFormatComponent::formatAsSum(
                $totalItems->getStartDate(), 
                $totalItems->getEndDate(), 
                $resultset, 
                $locationSetting->getOpenHours()
            );
            $data = $this->_nightclubHoursSwitch($data, $locationSetting);
            return new JsonResponse(['body' => $this->_formatResult($totalItems, __FUNCTION__, $data)]);
        }
        throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($totalItems->validationErrors));
    }

    public function transactions ()
    {
        $transactions = $this->getTransactions();
        $transactions->create($this->request->query, true);
        if ($transactions->validates()) {
            $locationSetting = $transactions->getLocationSetting();
            $resultset       = $transactions->getFromCache();

            $data = MetricFormatComponent::formatAsSum(
                $transactions->getStartDate(), 
                $transactions->getEndDate(), 
                $resultset, 
                $locationSetting->getOpenHours()
            );
            $data = $this->_nightclubHoursSwitch($data, $locationSetting);
            return new JsonResponse(['body' => $this->_formatResult($transactions, __FUNCTION__, $data)]);
        }
        throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($transactions->validationErrors));
    }

    public function revenue ()
    {
        $revenue = $this->getRevenue();
        $revenue->create($this->request->query, true);
        if ($revenue->validates()) {
            $locationSetting = $revenue->getLocationSetting();
            $resultset       = $revenue->getFromCache();

            $data = MetricFormatComponent::formatAsSum(
                $revenue->getStartDate(), 
                $revenue->getEndDate(), 
                $resultset, 
                $locationSetting->getOpenHours()
            );
            $data = $this->_nightclubHoursSwitch($data, $locationSetting);
            return new JsonResponse(['body' => $this->_formatResult($revenue, __FUNCTION__, $data)]);
        }
        throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($revenue->validationErrors));
    }

    public function returning ()
    {
        $returning = $this->getReturning();
        $returning->create($this->request->query, true);
        if ($returning->validates()) {
            $locationSetting = $returning->getLocationSetting();
            $resultset       = $returning->getFromCache();

            $data = MetricFormatComponent::formatAsSum(
                $returning->getStartDate(), 
                $returning->getEndDate(), 
                $resultset, 
                $locationSetting->getOpenHours()
            );
            $data = $this->_nightclubHoursSwitch($data, $locationSetting);
            return new JsonResponse(['body' => $this->_formatResult($returning, __FUNCTION__, $data)]);
        }
        throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($returning->validationErrors));
    }

    public function walkbys ()
    {
        $walkbys = $this->getWalkbys();
        $walkbys->create($this->request->query, true);
        if ($walkbys->validates()) {
            $locationSetting = $walkbys->getLocationSetting();
            $resultset       = $walkbys->getFromCache();

            $data = MetricFormatComponent::formatAsSum(
                $walkbys->getStartDate(), 
                $walkbys->getEndDate(), 
                $resultset, 
                $locationSetting->getOpenHours()
            );
            $data = $this->_nightclubHoursSwitch($data, $locationSetting);
            return new JsonResponse(['body' => $this->_formatResult($walkbys, __FUNCTION__, $data)]);
        }
        throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($walkbys->validationErrors));
    }

    public function windowConversion ()
    {
        $traffic = $this->getTraffic();
        $devices = $this->getDevices();
        $traffic->create($this->request->query, true);
        $devices->create($this->request->query, true);
        if ($traffic->validates() && $devices->validates()) {
            $locationSetting      = $traffic->getLocationSetting();
            $trafficResultset     = $traffic->getFromCache();
            $deviceItemsResultset = $devices->getFromCache();

            $data = MetricFormatComponent::formatAsRate(
                $traffic->getStartDate(), 
                $traffic->getEndDate(), 
                $trafficResultset, 
                $deviceItemsResultset, 
                $locationSetting->getOpenHours()
            );
            $data = $this->_nightclubHoursSwitch($data, $locationSetting);
            return new JsonResponse(['body' => $this->_formatResult($traffic, __FUNCTION__, $data)]);
        }
        throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($traffic->validationErrors));
    }

    public function totals ()
    {        
        $totals = $this->getTotals();
        $totals->create($this->request->query, true);
        if ($totals->validates()) {
            return new JsonResponse(['body' => $totals->getFromRaw()]);
        }
        throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($totals->validationErrors));
    }

    public function portalTraffic ()
    {
        $portalTraffic = $this->getPortalTraffic();
        $portalTraffic->create($this->request->query, true);
        if ($portalTraffic->validates()) {
            
            $locationSetting = $portalTraffic->getLocationSetting();
                        
            $portalTraffic->storeInCache($portalTraffic->getFromRaw());
            $resultset = $portalTraffic->getFromCache();
         
            $data      = MetricFormatComponent::formatAsSum(
                $portalTraffic->getStartDate(), 
                $portalTraffic->getEndDate(), 
                $resultset, 
                $locationSetting->getOpenHours()
            );
            $data      = $this->_nightclubHoursSwitch($data, $locationSetting);
            return new JsonResponse(['body' => $this->_formatResult($portalTraffic, __FUNCTION__, $data)]);
        }
        throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($portalTraffic->validationErrors));
    }

    public function monthlyTotals ()
    {
        $monthlyTotals = $this->getMonthlyTotals();
        $monthlyTotals->create($this->request->query, true);
        if ($monthlyTotals->validates()) {
            return new JsonResponse(['body' => $monthlyTotals->getFromRaw()]);
        }
        throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($monthlyTotals->validationErrors));
    }

    public function historicalTotals ()
    {
        $historicalTotals = $this->getHistoricalTotals();        
        $historicalTotals->create($this->request->query, true);
        if ($historicalTotals->validates()) {
            return new JsonResponse(['body' => $historicalTotals->getFromRaw()]);
        }
        throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($historicalTotals->validationErrors));
    }

}
