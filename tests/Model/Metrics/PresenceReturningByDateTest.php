<?php

class PresenceReturningByDateTest extends PHPUnit_Framework_TestCase
{

    protected function setUp ()
    {
        //FixtureManager::prepareTable('', '');
    }

    protected function tearDown ()
    {
        FixtureManager::prepareTable('rollups', 'presenceReturningByDate');
        FixtureManager::prepareTable('rollups', 'totals');
    }

    public function testExceptions ()
    {
        $presenceReturningByDate = new PresenceReturningByDate();

        try {
            $presenceReturningByDate->getFromRaw();
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $presenceReturningByDate->getFromCache();
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $presenceReturningByDate->storeInCache();
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testGetFromRaw ()
    {
        $presenceReturningByDate = new PresenceReturningByDate();
        $presenceReturningByDate->create([
            'PresenceReturningByDate' => [
                'location_id' => 689,
                'start_date'  => '2014-10-01',
                'end_date'    => '2014-10-30',
            ]], false);
        $result                  = $presenceReturningByDate->getFromRaw();
        $sDate                   = new DateTime('2014-10-01');
        $eDate                   = new DateTime('2014-10-30');
        foreach ($result as $date => $hours) {
            $cDate = new DateTime($date);
            $this->assertGreaterThanOrEqual($sDate, $cDate);
            $this->assertLessThanOrEqual($eDate, $cDate);
            foreach ($hours as $row) {
                $expected = ['value', 'date'];
                $this->assertEmpty(array_diff($expected, array_keys($row)));
            }
        }
    }

    public function testCache ()
    {
        $presenceReturningByDate = new PresenceReturningByDate();
        $presenceReturningByDate->create([
            'PresenceReturningByDate' => [
                'location_id' => 689,
                'start_date'  => '2014-10-01',
                'end_date'    => '2014-10-30',
            ]], false);

        //Test Insert
        $rawResult   = $presenceReturningByDate->getFromRaw();
        $presenceReturningByDate->storeInCache($rawResult);
        $cacheResult = $presenceReturningByDate->getFromCache();
        $this->compareResults($rawResult, $cacheResult);

        //Test update
        $rawResult['2014-10-01'][] = [
            'value' => 12,
            'hour'  => 23,
            'date'  => '2014-01-01'
        ];
        $presenceReturningByDate->storeInCache($rawResult);
        $cacheResult               = $presenceReturningByDate->getFromCache();
        $this->compareResults($rawResult, $cacheResult);
    }

    private function compareResults ($rawResult, $cacheResult)
    {
        $this->assertEquals(count($rawResult), count($cacheResult));
        foreach ($cacheResult as $row) {
            $this->assertEquals($row['location_id'], 689);
            $this->assertArrayHasKey($row['date'], $cacheResult);
            $this->assertArrayHasKey('date', $row);
            $this->assertArrayHasKey('total', $row);
            $totals = new Totals();
            $totals->create([
                'Totals' => [
                    'location_id' => 689,
                    'start_date'  => $row['date'],
                    'end_date'    => $row['date'],
                ]], false);
            $total  = $totals->getFromRaw();
            $this->assertEquals($total['presenceReturning'], $row['total']);
        }
    }

}
