<?php

class MonthlyTotalsTest extends PHPUnit_Framework_TestCase
{

    protected function setUp ()
    {
        //FixtureManager::prepareTable('', '');
    }

    protected function tearDown ()
    {
        FixtureManager::prepareTable('rollups', 'revenue');
        FixtureManager::prepareTable('rollups', 'totals');
        FixtureManager::prepareTable('rollups', 'revenue');
        FixtureManager::prepareTable('rollups', 'transactions');
        FixtureManager::prepareTable('rollups', 'portalTraffic');
        FixtureManager::prepareTable('rollups', 'presenceTrafficByHour');
        FixtureManager::prepareTable('rollups', 'presenceTrafficByDate');
        FixtureManager::prepareTable('rollups', 'totals');
    }

    public function testExceptions ()
    {
        $monthlyTotals = new MonthlyTotals();
        try {
            $monthlyTotals->getFromRaw();
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testIsMonth ()
    {
        $months        = [
            '01', '02', '03', '04', '05', '06',
            '07', '08', '09', '10', '11', '12'
        ];
        $monthlyTotals = new MonthlyTotals();
        foreach ($months as $month) {
            $this->assertTrue($monthlyTotals->isMonth(['month' => $month]));
        }
    }

    public function testIsYear ()
    {
        $years         = ['2013', '2014', '2015'];
        $monthlyTotals = new MonthlyTotals();
        foreach ($years as $year) {
            $this->assertTrue($monthlyTotals->isYear(['year' => $year]));
        }
    }

    public function testYear ()
    {
        $monthlyTotals = new MonthlyTotals();
        $this->assertEquals(null, $monthlyTotals->getYear());
        $monthlyTotals->setYear('2014');
        $this->assertEquals('2014', $monthlyTotals->getYear());
    }

    public function testMonth ()
    {
        $monthlyTotals = new MonthlyTotals();
        $this->assertEquals(null, $monthlyTotals->getMonth());
        $monthlyTotals->setMonth('12');
        $this->assertEquals('12', $monthlyTotals->getMonth());
    }

    public function testGetFromRaw ()
    {
        $data          = [
            'location_id' => 689,
            'start_date'  => '2014-01-01',
            'end_date'    => '2014-01-31',
        ];
        $revenue       = new Revenue();
        $transactions  = new Transactions();
        $portalTraffic = new PortalTraffic();

        $revenue->create(['Revenue' => $data]);
        $transactions->create(['Transactions' => $data]);
        $portalTraffic->create(['PortalTraffic' => $data]);

        $revenue->storeInCache($revenue->getFromRaw());
        $transactions->storeInCache($transactions->getFromRaw());
        $portalTraffic->storeInCache($portalTraffic->getFromRaw());

        $monthlyTotals = new MonthlyTotals();
        $monthlyTotals->create(['MonthlyTotals' => ['location_id' => 689, 'month' => '01', 'year' => '2014']]);
        $result        = $monthlyTotals->getFromRaw();
        $this->validateResult($result);
    }

    public function validateResult ($result)
    {
        $expected = ['data', 'options'];
        $this->assertEmpty(array_diff($expected, array_keys($result)));        

        $expected = ['endpoint', 'year', 'month', 'location_id'];        
        $this->assertEmpty(array_diff($expected, array_keys($result['options'])));
        
        $this->assertEmpty(array_diff(['breakdown', 'totals'], array_keys($result['data'])));
        $this->assertGreaterThanOrEqual(4, count($result['data']['breakdown']));
        $this->assertLessThanOrEqual(5, count($result['data']['breakdown']));
        foreach ($result['data']['breakdown'] as $week) {
            $expected = [
                'revenue',
                'visitors',
                'conversionRate',
                'avgRevenueDaily',
                'avgVisitorsDaily',
                'avgConversionRateDaily',
                'start_date',
                'end_date'
            ];            
            $this->assertEmpty(array_diff($expected, array_keys($week)));
            $this->assertGreaterThanOrEqual(new DateTime($week['start_date']), new DateTime($week['end_date']));
        }
    }

}
