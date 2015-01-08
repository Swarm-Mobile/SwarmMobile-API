<?php

class PresenceTrafficByHourTest extends PHPUnit_Framework_TestCase
{

    protected function setUp ()
    {
        //FixtureManager::prepareTable('', '');
    }

    protected function tearDown ()
    {
        FixtureManager::prepareTable('rollups', 'presenceTrafficByHour');
    }

    public function testExceptions ()
    {
        $presenceTrafficByHour = new PresenceTrafficByHour();

        try {
            $presenceTrafficByHour->getFromRaw();
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $presenceTrafficByHour->getFromCache();
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $presenceTrafficByHour->storeInCache();
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testGetFromRaw ()
    {
        $presenceTrafficByHour = new PresenceTrafficByHour();
        $presenceTrafficByHour->create([
            'PresenceTrafficByHour' => [
                'location_id' => 689,
                'start_date'  => '2014-10-01',
                'end_date'    => '2014-10-30',
            ]], false);
        $result                = $presenceTrafficByHour->getFromRaw();
        $sDate                 = new DateTime('2014-10-01');
        $eDate                 = new DateTime('2014-10-30');
        foreach ($result as $date => $hours) {
            $cDate = new DateTime($date);
            $this->assertGreaterThanOrEqual($sDate, $cDate);
            $this->assertLessThanOrEqual($eDate, $cDate);
            foreach ($hours as $row) {
                $expected = ['value', 'hour'];
                $this->assertEmpty(array_diff($expected, array_keys($row)));
                $this->assertGreaterThanOrEqual(0, (int) $row['hour']);
                $this->assertLessThanOrEqual(23, (int) $row['hour']);
            }
        }
    }

    public function testCache ()
    {
        $presenceTrafficByHour = new PresenceTrafficByHour();
        $presenceTrafficByHour->create([
            'PresenceTrafficByHour' => [
                'location_id' => 689,
                'start_date'  => '2014-10-01',
                'end_date'    => '2014-10-30',
            ]], false);

        //Test Insert
        $rawResult   = $presenceTrafficByHour->getFromRaw();
        $presenceTrafficByHour->storeInCache($rawResult);
        $cacheResult = $presenceTrafficByHour->getFromCache();
        $this->compareResults($rawResult, $cacheResult);

        //Test update
        $rawResult['2014-10-01'][23] = [
            'value' => 12,
            'hour'  => 23
        ];
        $presenceTrafficByHour->storeInCache($rawResult);
        $cacheResult                 = $presenceTrafficByHour->getFromCache();
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
        }
    }

}
