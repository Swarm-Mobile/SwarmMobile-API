<?php

class RollupsShellTest extends PHPUnit_Framework_TestCase
{
    protected function setUp ()
    {
        //FixtureManager::prepareTable('', '');
    }

    protected function tearDown ()
    {
        FixtureManager::prepareTable('rollups', 'avgTicket');
        FixtureManager::prepareTable('rollups', 'devices');
        FixtureManager::prepareTable('rollups', 'dwell');
        FixtureManager::prepareTable('rollups', 'emailsCaptured');
        FixtureManager::prepareTable('rollups', 'itemsPerTransaction');
        FixtureManager::prepareTable('rollups', 'portalTraffic');
        FixtureManager::prepareTable('rollups', 'presenceReturningByDate');
        FixtureManager::prepareTable('rollups', 'presenceReturningByHour');
        FixtureManager::prepareTable('rollups', 'presenceTrafficByDate');
        FixtureManager::prepareTable('rollups', 'presenceTrafficByHour');
        FixtureManager::prepareTable('rollups', 'returning');
        FixtureManager::prepareTable('rollups', 'revenue');
        FixtureManager::prepareTable('rollups', 'timeInShop');
        FixtureManager::prepareTable('rollups', 'totalItems');
        FixtureManager::prepareTable('rollups', 'totals');
        FixtureManager::prepareTable('rollups', 'traffic');
        FixtureManager::prepareTable('rollups', 'transactions');
        FixtureManager::prepareTable('rollups', 'walkbys');
        FixtureManager::prepareTable('rollups', 'wifiConnections');
    }
    
    public function testMain()
    {
        $rollupsShell = new RollupShell();
        $rollupsShell->output = false;
        $rollupsShell->params = [
            'location_id' => 689,
            'start_date' => '2014-10-01',
            'end_date' => '2014-10-02',            
        ];
        $rollupsShell->main();
    }

}
