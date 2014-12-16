<?php

class PresenceReturningTest extends PHPUnit_Framework_TestCase
{

    protected function setUp ()
    {
        //FixtureManager::prepareTable('', '');
    }

    protected function tearDown ()
    {
        FixtureManager::prepareTable('rollups', 'presenceReturningByHour');
        FixtureManager::prepareTable('rollups', 'presenceReturningByDate');
        FixtureManager::prepareTable('rollups', 'totals');
    }

    public function testExceptions ()
    {
        $presenceReturning = new PresenceReturning();

        try {
            $presenceReturning->getFromRaw();
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $presenceReturning->getFromCache();
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $presenceReturning->storeInCache();
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testGetFromCache ()
    {
        $data                    = [
            'location_id' => 689,
            'start_date'  => '2014-10-01',
            'end_date'    => '2014-10-30',
        ];
        $presenceReturning       = new PresenceReturning();
        $presenceReturningByHour = new PresenceReturningByHour();
        $presenceReturningByDate = new PresenceReturningByDate();
        $presenceReturning->create(['PresenceReturning' => $data], false);
        $presenceReturningByHour->create(['PresenceReturningByHour' => $data], false);
        $presenceReturningByDate->create(['PresenceReturningByDate' => $data], false);
        $presenceReturningByDate->storeInCache($presenceReturningByDate->getFromRaw());
        $presenceReturningByHour->storeInCache($presenceReturningByHour->getFromRaw());
        $result                  = $presenceReturning->getFromCache();
        $resultByDate            = $presenceReturningByDate->getFromCache();
        $resultByHour            = $presenceReturningByHour->getFromCache();
        foreach ($result as $date => $row) {
            foreach ($row as $k => $v) {
                if ($k == 'total_total' && isset($resultByDate[$date])) {
                    $this->assertEquals($v, $resultByDate[$date]['total']);
                }
                else {
                    $this->assertEquals($v, $resultByHour[$date][$k]);
                }
            }
        }
    }

}
