<?php

class WalkbysTest extends PHPUnit_Framework_TestCase
{

    protected function setUp ()
    {
        //FixtureManager::prepareTable('', '');
    }

    protected function tearDown ()
    {
        FixtureManager::prepareTable('rollups', 'walkbys');
        FixtureManager::prepareTable('rollups', 'totals');
    }

    public function testExceptions ()
    {
        $walkbys = new Walkbys();

        try {
            $walkbys->getFromRaw();
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $walkbys->getFromCache();
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $walkbys->storeInCache();
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testGetFromRaw ()
    {
        $walkbys = new Walkbys();
        $walkbys->create([
            'Walkbys' => [
                'location_id' => 689,
                'start_date'  => '2014-10-01',
                'end_date'    => '2014-10-30',
            ]], false);
        $result  = $walkbys->getFromRaw();
        $sDate   = new DateTime('2014-10-01');
        $eDate   = new DateTime('2014-10-30');
        foreach ($result as $date => $hours) {
            $cDate = new DateTime($date);
            $this->assertGreaterThanOrEqual($sDate, $cDate);
            $this->assertLessThanOrEqual($eDate, $cDate);
            foreach ($hours as $row) {
                $expected = ['value', 'date', 'hour'];
                $this->assertEmpty(array_diff($expected, array_keys($row)));
                $this->assertGreaterThanOrEqual(0, (int) $row['hour']);
                $this->assertLessThanOrEqual(23, (int) $row['hour']);
            }
        }
    }

    public function testCache ()
    {
        $walkbys = new Walkbys();
        $walkbys->create([
            'Walkbys' => [
                'location_id' => 689,
                'start_date'  => '2014-10-01',
                'end_date'    => '2014-10-30',
            ]], false);

        //Test Insert
        $rawResult   = $walkbys->getFromRaw();
        $walkbys->storeInCache($rawResult);
        $cacheResult = $walkbys->getFromCache();
        $this->compareResults($rawResult, $cacheResult);

        //Test update
        $rawResult['2014-10-01'][] = [
            'value' => 12,
            'hour'  => 23,
            'date'  => '2014-01-01'
        ];
        $walkbys->storeInCache($rawResult);
        $cacheResult               = $walkbys->getFromCache();
        $this->compareResults($rawResult, $cacheResult);
    }

    private function compareResults ($rawResult, $cacheResult)
    {
        $this->assertEquals(count($rawResult), count($cacheResult));
        foreach ($cacheResult as $row) {
            $this->assertEquals($row['location_id'], 689);
            $this->assertArrayHasKey($row['date'], $cacheResult);
            foreach ($rawResult[$row['date']] as $hours) {
                $i = 'h' . ($hours['hour'] < 10 ? '0' : '') . $hours['hour'];
                $this->assertEquals($hours['value'], $row[$i]);
            }
            $totals = new Totals();
            $totals->create([
                'Totals' => [
                    'location_id' => 689,
                    'start_date'  => $row['date'],
                    'end_date'    => $row['date'],
                ]], false);
            $total  = $totals->getFromRaw();
            $this->assertEquals($total['walkbys'], $row['total_open']);
        }
    }

}
