<?php

class HistoricalTotalsTest extends PHPUnit_Framework_TestCase
{

    protected function setUp ()
    {
        //FixtureManager::prepareTable('', '');
    }

    protected function tearDown ()
    {
        FixtureManager::prepareTable('rollups', 'revenue');
        FixtureManager::prepareTable('rollups', 'totals');
        FixtureManager::prepareTable('portal', 'visitorEvent');
        FixtureManager::prepareTable('rollups', 'revenue');
        FixtureManager::prepareTable('rollups', 'transactions');
        FixtureManager::prepareTable('rollups', 'portalTraffic');
        FixtureManager::prepareTable('rollups', 'presenceTrafficByHour');
        FixtureManager::prepareTable('rollups', 'presenceTrafficByDate');
        FixtureManager::prepareTable('rollups', 'totals');
    }

    public function testExceptions ()
    {
        $historicalTotals = new HistoricalTotals();
        try {
            $historicalTotals->getFromRaw();
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testGetFromRawVisitorEvents ()
    {
        $data          = [
            'location_id' => 367,
            'start_date'  => '2014-10-01',
            'end_date'    => '2014-10-30',
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

        $historicalTotals = new HistoricalTotals();
        $historicalTotals->create(['HistoricalTotals' => ['location_id' => 367]]);
        $result           = $historicalTotals->getFromRaw();
        $this->validateResult($result);
    }

    public function testGetFromRawTransactions ()
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

        $historicalTotals = new HistoricalTotals();
        $historicalTotals->create(['HistoricalTotals' => ['location_id' => 689]]);
        $result           = $historicalTotals->getFromRaw();
        $this->validateResult($result);
    }

    private function validateResult ($result)
    {
        $expected = ['data', 'options'];
        $this->assertEmpty(array_diff($expected, array_keys($result)));

        $expected = ['location_id', 'endpoint'];
        $this->assertEmpty(array_diff($expected, array_keys($result['options'])));

        $this->assertEquals(['totals'], array_keys($result['data']));
        
        $expected = [
            'revenue',
            'transactions',
            'visitors',
            'conversionRate',
            'avgTransactionsDaily',
            'avgTransactionsWeekly',
            'avgTransactionsMonthly',
            'avgRevenueDaily',
            'avgRevenueWeekly',
            'avgRevenueMonthly',
            'avgVisitorsDaily',
            'avgVisitorsWeekly',
            'avgVisitorsMonthly',
            'avgConversionRateDaily',
            'avgConversionRateWeekly',
            'avgConversionRateMonthly',
        ];        
        $this->assertEmpty(array_diff($expected, array_keys($result['data']['totals'])));
    }

}
