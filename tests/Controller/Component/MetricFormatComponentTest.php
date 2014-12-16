<?php

class MetricFormatComponentTest extends PHPUnit_Framework_TestCase
{

    public $locationId       = 689;
    public $date             = '2014-01-01';
    public $startDate        = '2014-01-02';
    public $endDate          = '2014-01-07';    
    public $openHours        = [
        'monday'    => ['isOpen' => true, 'open' => '09:00', 'close' => '21:00'],
        'tuesday'   => ['isOpen' => true, 'open' => '09:00', 'close' => '21:00'],
        'wednesday' => ['isOpen' => false, 'open' => '0', 'close' => '0'],
        'thursday'  => ['isOpen' => true, 'open' => '09:00', 'close' => '21:00'],
        'friday'    => ['isOpen' => false, 'open' => '0', 'close' => '0'],
        'saturday'  => ['isOpen' => true, 'open' => '09:00', 'close' => '21:00'],
        'sunday'    => ['isOpen' => true, 'open' => '09:00', 'close' => '21:00'],
    ];

    private function getBaseResultSet ()
    {
        $baseResultSet = [
            '2014-01-02' => MetricFormatComponent::emptyDayResult($this->locationId, '2014-01-02'),
            '2014-01-04' => MetricFormatComponent::emptyDayResult($this->locationId, '2014-01-04'),
            '2014-01-06' => MetricFormatComponent::emptyDayResult($this->locationId, '2014-01-06'),
            '2014-01-07' => MetricFormatComponent::emptyDayResult($this->locationId, '2014-01-07'),
        ];

        $baseResultSet['2014-01-02']['h09'] = 3;
        $baseResultSet['2014-01-02']['h12'] = 7;
        $baseResultSet['2014-01-02']['total_open'] = 10;
        $baseResultSet['2014-01-02']['total_close'] = 0;
        $baseResultSet['2014-01-02']['total_total'] = 10;

        $baseResultSet['2014-01-04']['h03'] = 22;
        $baseResultSet['2014-01-04']['h05'] = 32;
        $baseResultSet['2014-01-04']['total_open'] = 0;
        $baseResultSet['2014-01-04']['total_close'] = 54;
        $baseResultSet['2014-01-04']['total_total'] = 54;

        $baseResultSet['2014-01-06']['h07'] = 80;
        $baseResultSet['2014-01-06']['h23'] = 40;
        $baseResultSet['2014-01-06']['total_open'] = 0;
        $baseResultSet['2014-01-06']['total_close'] = 120;
        $baseResultSet['2014-01-06']['total_total'] = 120;

        $baseResultSet['2014-01-07']['h07'] = 41;
        $baseResultSet['2014-01-07']['h23'] = 52;
        $baseResultSet['2014-01-07']['total_open'] = 0;
        $baseResultSet['2014-01-07']['total_close'] = 93;
        $baseResultSet['2014-01-07']['total_total'] = 93;

        return $baseResultSet;
    }

    private function getDivisorResultset ()
    {
        $divisorResultSet = [
            '2014-01-03' => MetricFormatComponent::emptyDayResult($this->locationId, '2014-01-03'),
            '2014-01-04' => MetricFormatComponent::emptyDayResult($this->locationId, '2014-01-04'),
            '2014-01-06' => MetricFormatComponent::emptyDayResult($this->locationId, '2014-01-06'),
        ];

        $divisorResultSet['2014-01-03']['h09'] = 3;
        $divisorResultSet['2014-01-03']['h12'] = 7;
        $divisorResultSet['2014-01-03']['total_open'] = 10;
        $divisorResultSet['2014-01-03']['total_close'] = 0;
        $divisorResultSet['2014-01-03']['total_total'] = 10;

        $divisorResultSet['2014-01-04']['h03'] = 22;
        $divisorResultSet['2014-01-04']['h07'] = 33;
        $divisorResultSet['2014-01-04']['total_open'] = 0;
        $divisorResultSet['2014-01-04']['total_close'] = 55;
        $divisorResultSet['2014-01-04']['total_total'] = 55;

        $divisorResultSet['2014-01-06']['h07'] = 40;
        $divisorResultSet['2014-01-06']['h23'] = 50;
        $divisorResultSet['2014-01-06']['total_open'] = 0;
        $divisorResultSet['2014-01-06']['total_close'] = 90;
        $divisorResultSet['2014-01-06']['total_total'] = 90;

        return $divisorResultSet;
    }

    private function getModifications(){
        return [
            '2014-01-02' => ['09','12'],
            '2014-01-03' => ['09','12'],
            '2014-01-04' => ['03','05','07'],
            '2014-01-06' => ['07','23'],
            '2014-01-07' => ['07','23'],
        ];
    }
    
    private function formatResultNonModifiedAssertions($result){
        $modifications = $this->getModifications();
        foreach($result['breakdown'] as $date => $values){            
            if(!isset($modifications[$date])){                
                $this->assertEquals($result['breakdown'][$date]['totals']['open'], 0);
                $this->assertEquals($result['breakdown'][$date]['totals']['close'], 0);
                $this->assertEquals($result['breakdown'][$date]['totals']['total'], 0);
            }
            foreach($values['hours'] as $hour => $totals){                
                if(!isset($modifications[$date]) || !in_array($hour,$modifications[$date])){                    
                    $this->assertEquals($totals['total'], 0);
                }
            }
        }
    }
    
    public function testEmptyDayResult ()
    {        
        $result = MetricFormatComponent::emptyDayResult($this->locationId, $this->date);
        $this->assertEquals($result['date'], $this->date);
        $this->assertEquals($result['location_id'], $this->locationId);
        $this->assertEquals($result['total_open'], 0);
        $this->assertEquals($result['total_close'], 0);
        $this->assertEquals($result['total_total'], 0);
        $this->assertEquals($result['h00'], 0);
        $this->assertEquals($result['h01'], 0);
        $this->assertEquals($result['h02'], 0);
        $this->assertEquals($result['h03'], 0);
        $this->assertEquals($result['h04'], 0);
        $this->assertEquals($result['h05'], 0);
        $this->assertEquals($result['h06'], 0);
        $this->assertEquals($result['h07'], 0);
        $this->assertEquals($result['h08'], 0);
        $this->assertEquals($result['h09'], 0);
        $this->assertEquals($result['h10'], 0);
        $this->assertEquals($result['h11'], 0);
        $this->assertEquals($result['h12'], 0);
        $this->assertEquals($result['h13'], 0);
        $this->assertEquals($result['h14'], 0);
        $this->assertEquals($result['h15'], 0);
        $this->assertEquals($result['h16'], 0);
        $this->assertEquals($result['h17'], 0);
        $this->assertEquals($result['h18'], 0);
        $this->assertEquals($result['h19'], 0);
        $this->assertEquals($result['h20'], 0);
        $this->assertEquals($result['h21'], 0);
        $this->assertEquals($result['h22'], 0);
        $this->assertEquals($result['h23'], 0);
    }

    public function testEmptyDayTotalResult ()
    {
        $result   = MetricFormatComponent::emptyDayTotalResult(
                        $this->locationId, $this->date
        );
        $this->assertEquals($result['date'], $this->date);
        $this->assertEquals($result['location_id'], $this->locationId);
        $expected = [
            'walkbys'                => 0,
            'sensorTraffic'          => 0,
            'transactions'           => 0,
            'revenue'                => 0,
            'totalItems'             => 0,
            'returning'              => 0,
            'footTraffic'            => 0,
            'presenceTraffic'        => 0,
            'portalTraffic'          => 0,
            'timeInShop'             => 0,
            'traffic'                => 0,
            'devices'                => 0,
            'itemsPerTransaction'    => 0,
            'windowConversion'       => 0,
            'avgTicket'              => 0,
            'conversionRate'         => 0,
            'presenceConversionRate' => 0,
            'portalConversionRate'   => 0,
            'dwell'                  => 0,
        ];
        foreach ($expected as $k => $v) {
            $this->assertEquals($result[$k], $v);
        }
    }

    public function testEmptyHistoricalTotals ()
    {
        $result   = MetricFormatComponent::emptyHistoricalTotals();
        $expected = [
            "revenue"                  => 0,
            "transactions"             => 0,
            "visitors"                 => 0,
            "conversionRate"           => 0,
            "avgTransactionsDaily"     => 0,
            "avgTransactionsWeekly"    => 0,
            "avgTransactionsMonthly"   => 0,
            "avgRevenueDaily"          => 0,
            "avgRevenueWeekly"         => 0,
            "avgRevenueMonthly"        => 0,
            "avgVisitorsDaily"         => 0,
            "avgVisitorsWeekly"        => 0,
            "avgVisitorsMonthly"       => 0,
            "avgConversionRateDaily"   => 0,
            "avgConversionRateWeekly"  => 0,
            "avgConversionRateMonthly" => 0
        ];
        $this->assertEquals($result, $expected);
    }

    public function testEmptyMonthlyTotals ()
    {
        $result   = MetricFormatComponent::emptyMonthlyTotals();
        $expected = [
            'breakdown' => [],
            'totals'    => [
                'revenue'                => 0,
                'visitors'               => 0,
                'conversionRate'         => 0,
                'avgRevenueDaily'        => 0,
                'avgVisitorsDaily'       => 0,
                'avgConversionRateDaily' => 0,
            ]
        ];
        $this->assertEquals($result, $expected);
    }

    public function testFormatAsRate ()
    {

        $result = MetricFormatComponent::formatAsRate(
            $this->startDate, 
            $this->endDate, 
            $this->getBaseResultset(), 
            $this->getDivisorResultset(), 
            $this->openHours
        );
        
        $this->formatResultNonModifiedAssertions($result);

        $this->assertEquals($result['breakdown']['2014-01-02']['hours']['09']['total'], 100);
        $this->assertEquals($result['breakdown']['2014-01-02']['hours']['12']['total'], 100);
        $this->assertEquals($result['breakdown']['2014-01-02']['totals']['open'], 100);
        $this->assertEquals($result['breakdown']['2014-01-02']['totals']['close'], 0);
        $this->assertEquals($result['breakdown']['2014-01-02']['totals']['total'], 100);

        $this->assertEquals($result['breakdown']['2014-01-03']['hours']['09']['total'], 0);
        $this->assertEquals($result['breakdown']['2014-01-03']['hours']['12']['total'], 0);
        $this->assertEquals($result['breakdown']['2014-01-03']['totals']['open'], 0);
        $this->assertEquals($result['breakdown']['2014-01-03']['totals']['close'], 0);
        $this->assertEquals($result['breakdown']['2014-01-03']['totals']['total'], 0);

        $this->assertEquals($result['breakdown']['2014-01-04']['hours']['03']['total'], 100);
        $this->assertEquals($result['breakdown']['2014-01-04']['hours']['05']['total'], 100);
        $this->assertEquals($result['breakdown']['2014-01-04']['hours']['07']['total'], 0);
        $this->assertEquals($result['breakdown']['2014-01-04']['totals']['open'], 0);
        $this->assertEquals($result['breakdown']['2014-01-04']['totals']['close'], 98.18);
        $this->assertEquals($result['breakdown']['2014-01-04']['totals']['total'], 98.18);

        $this->assertEquals($result['breakdown']['2014-01-06']['hours']['07']['total'], 100);
        $this->assertEquals($result['breakdown']['2014-01-06']['hours']['23']['total'], 80);
        $this->assertEquals($result['breakdown']['2014-01-06']['totals']['open'], 0);
        $this->assertEquals($result['breakdown']['2014-01-06']['totals']['close'], 100);
        $this->assertEquals($result['breakdown']['2014-01-06']['totals']['total'], 100);

        $this->assertEquals($result['breakdown']['2014-01-07']['hours']['07']['total'], 100);
        $this->assertEquals($result['breakdown']['2014-01-07']['hours']['23']['total'], 100);
        $this->assertEquals($result['breakdown']['2014-01-07']['totals']['open'], 0);
        $this->assertEquals($result['breakdown']['2014-01-07']['totals']['close'], 100);
        $this->assertEquals($result['breakdown']['2014-01-07']['totals']['total'], 100);
                
        $this->assertEquals($result['totals']['open'], 100);
        $this->assertEquals($result['totals']['close'], 100);
        $this->assertEquals($result['totals']['total'], 100);
        
    }

    public function testFormatAsRatio ()
    {
        $result = MetricFormatComponent::formatAsRatio(
            $this->startDate, 
            $this->endDate, 
            $this->getBaseResultset(), 
            $this->getDivisorResultset(), 
            $this->openHours
        );

        $this->assertEquals($result['breakdown']['2014-01-02']['hours']['09']['total'], 3);
        $this->assertEquals($result['breakdown']['2014-01-02']['hours']['12']['total'], 7);
        $this->assertEquals($result['breakdown']['2014-01-02']['totals']['open'], 10);
        $this->assertEquals($result['breakdown']['2014-01-02']['totals']['close'], 0);
        $this->assertEquals($result['breakdown']['2014-01-02']['totals']['total'], 10);

        $this->assertEquals($result['breakdown']['2014-01-03']['hours']['09']['total'], 0);
        $this->assertEquals($result['breakdown']['2014-01-03']['hours']['12']['total'], 0);
        $this->assertEquals($result['breakdown']['2014-01-03']['totals']['open'], 0);
        $this->assertEquals($result['breakdown']['2014-01-03']['totals']['close'], 0);
        $this->assertEquals($result['breakdown']['2014-01-03']['totals']['total'], 0);

        $this->assertEquals($result['breakdown']['2014-01-04']['hours']['03']['total'], 1);
        $this->assertEquals($result['breakdown']['2014-01-04']['hours']['05']['total'], 32);
        $this->assertEquals($result['breakdown']['2014-01-04']['hours']['07']['total'], 0);
        $this->assertEquals($result['breakdown']['2014-01-04']['totals']['open'], 0);
        $this->assertEquals($result['breakdown']['2014-01-04']['totals']['close'], 0.98);
        $this->assertEquals($result['breakdown']['2014-01-04']['totals']['total'], 0.98);

        $this->assertEquals($result['breakdown']['2014-01-06']['hours']['07']['total'], 2);
        $this->assertEquals($result['breakdown']['2014-01-06']['hours']['23']['total'], 0.8);
        $this->assertEquals($result['breakdown']['2014-01-06']['totals']['open'], 0);
        $this->assertEquals($result['breakdown']['2014-01-06']['totals']['close'], 1.33);
        $this->assertEquals($result['breakdown']['2014-01-06']['totals']['total'], 1.33);

        $this->assertEquals($result['breakdown']['2014-01-07']['hours']['07']['total'], 41);
        $this->assertEquals($result['breakdown']['2014-01-07']['hours']['23']['total'], 52);
        $this->assertEquals($result['breakdown']['2014-01-07']['totals']['open'], 0);
        $this->assertEquals($result['breakdown']['2014-01-07']['totals']['close'], 93);
        $this->assertEquals($result['breakdown']['2014-01-07']['totals']['total'], 93);
        
        $this->assertEquals($result['totals']['open'], 1);
        $this->assertEquals($result['totals']['close'], 1.84);
        $this->assertEquals($result['totals']['total'], 1.79);
    }

    public function testFormatAsSum ()
    {
        $result = MetricFormatComponent::formatAsSum(
            $this->startDate, 
            $this->endDate, 
            $this->getBaseResultset(),                 
            $this->openHours
        );

        $this->assertEquals($result['breakdown']['2014-01-02']['hours']['09']['total'], 3);
        $this->assertEquals($result['breakdown']['2014-01-02']['hours']['12']['total'], 7);
        $this->assertEquals($result['breakdown']['2014-01-02']['totals']['open'], 10);
        $this->assertEquals($result['breakdown']['2014-01-02']['totals']['close'], 0);
        $this->assertEquals($result['breakdown']['2014-01-02']['totals']['total'], 10);

        $this->assertEquals($result['breakdown']['2014-01-04']['hours']['03']['total'], 22);
        $this->assertEquals($result['breakdown']['2014-01-04']['hours']['05']['total'], 32);
        $this->assertEquals($result['breakdown']['2014-01-04']['totals']['open'], 0);
        $this->assertEquals($result['breakdown']['2014-01-04']['totals']['close'], 54);
        $this->assertEquals($result['breakdown']['2014-01-04']['totals']['total'], 54);

        $this->assertEquals($result['breakdown']['2014-01-06']['hours']['07']['total'], 80);
        $this->assertEquals($result['breakdown']['2014-01-06']['hours']['23']['total'], 40);
        $this->assertEquals($result['breakdown']['2014-01-06']['totals']['open'], 0);
        $this->assertEquals($result['breakdown']['2014-01-06']['totals']['close'], 120);
        $this->assertEquals($result['breakdown']['2014-01-06']['totals']['total'], 120);

        $this->assertEquals($result['breakdown']['2014-01-07']['hours']['07']['total'], 41);
        $this->assertEquals($result['breakdown']['2014-01-07']['hours']['23']['total'], 52);
        $this->assertEquals($result['breakdown']['2014-01-07']['totals']['open'], 0);
        $this->assertEquals($result['breakdown']['2014-01-07']['totals']['close'], 93);
        $this->assertEquals($result['breakdown']['2014-01-07']['totals']['total'], 93);
        
        $this->assertEquals($result['totals']['open'], 10);
        $this->assertEquals($result['totals']['close'], 267);
        $this->assertEquals($result['totals']['total'], 277);
    }

    public function testNightclubHoursSwitch(){
        $result = MetricFormatComponent::formatAsSum(
            $this->startDate, 
            $this->endDate, 
            $this->getBaseResultset(),                 
            $this->openHours
        );        
        
        $nighclubTimezoneTranslations = [
          'eastcoast_time'      => 18,  
          'eastcost_time'       => 18,  
          'central_time'        => 19,
          'mountain_time'       => 20,
          'pacific_time'        => 21,
          'eastaustralian_time' => 3,
        ];
        
        foreach($nighclubTimezoneTranslations as $timezone => $diffHours){
            $cResult =  MetricFormatComponent::nightclubHoursSwitch($result, 'Pacific/Samoa', 'yes', $timezone);
            foreach($result['breakdown'] as $date => $values){
                foreach($values['hours'] as $hour => $totals){
                    $newHour = ($hour + (24 - $diffHours))%24;                    
                    $newHour = ((int)$newHour < 10)?('0'.(int)$newHour):($newHour.'');
                    $this->assertEquals(
                        $result['breakdown'][$date]['hours'][$hour], 
                        $cResult['breakdown'][$date]['hours'][$newHour]
                    );                            
                }
                $this->assertEquals($result['breakdown'][$date]['totals'], $cResult['breakdown'][$date]['totals']);
            }
            $this->assertEquals($result['totals'], $cResult['totals']);
        }
        
        $cResult =  MetricFormatComponent::nightclubHoursSwitch($result, 'Pacific/Samoa', 'no', 'eastcoast_time');
        $this->assertEquals($result, $cResult);
        $cResult =  MetricFormatComponent::nightclubHoursSwitch($result, 'Pacific/Samoa', 'no', 'whatever');
        $this->assertEquals($result, $cResult);
        $cResult =  MetricFormatComponent::nightclubHoursSwitch($result, 'Pacific/Samoa', 'yes', 'whatever');
        $this->assertEquals($result, $cResult);        
        
    }
    
    public function testRate(){
        $this->assertEquals(MetricFormatComponent::rate(8, 4), 100);
        $this->assertEquals(MetricFormatComponent::rate(8, 0), 100);
        $this->assertEquals(MetricFormatComponent::rate(8, 10), 80);
        $this->assertEquals(MetricFormatComponent::rate(0, 0), 0);
        $this->assertEquals(MetricFormatComponent::rate(0, 1), 0);
    }   
    
    public function testRatio(){        
        $this->assertEquals(MetricFormatComponent::ratio(8, 4), 2);
        $this->assertEquals(MetricFormatComponent::ratio(8, 0), 8);
        $this->assertEquals(MetricFormatComponent::ratio(0, 0), 0);
        $this->assertEquals(MetricFormatComponent::ratio(0, 1), 0);
    }
}
