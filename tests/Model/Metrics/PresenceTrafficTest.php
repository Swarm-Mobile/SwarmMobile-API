<?php

class PresenceTrafficTest extends PHPUnit_Framework_TestCase
{

    protected function setUp ()
    {
        //FixtureManager::prepareTable('', '');
    }

    protected function tearDown ()
    {
        FixtureManager::prepareTable('rollups', 'presenceTrafficByHour');
        FixtureManager::prepareTable('rollups', 'presenceTrafficByDate');
        FixtureManager::prepareTable('rollups', 'totals');
        FixtureManager::prepareTable('swarm_backstage', 'location');
    }

    public function testExceptions ()
    {
        $presenceTraffic = new PresenceTraffic();

        try {
            $presenceTraffic->getFromRaw();
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $presenceTraffic->getFromCache();
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $presenceTraffic->storeInCache();
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testGetFromCache ()
    {
        $data                  = [
            'location_id' => 689,
            'start_date'  => '2014-10-01',
            'end_date'    => '2014-10-30',
        ];
        $presenceTraffic       = new PresenceTraffic();
        $presenceTrafficByHour = new PresenceTrafficByHour();
        $presenceTrafficByDate = new PresenceTrafficByDate();
        $presenceTraffic->create(['PresenceTraffic' => $data], false);
        $presenceTrafficByHour->create(['PresenceTrafficByHour' => $data], false);
        $presenceTrafficByDate->create(['PresenceTrafficByDate' => $data], false);
        $presenceTrafficByDate->storeInCache($presenceTrafficByDate->getFromRaw());
        $presenceTrafficByHour->storeInCache($presenceTrafficByHour->getFromRaw());
        $result                = $presenceTraffic->getFromCache();
        $resultByDate          = $presenceTrafficByDate->getFromCache();
        $resultByHour          = $presenceTrafficByHour->getFromCache();
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
