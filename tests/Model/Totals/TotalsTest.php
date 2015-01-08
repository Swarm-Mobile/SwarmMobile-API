<?php

class TotalsTest extends PHPUnit_Framework_TestCase
{
    protected function setUp ()
    {
        //FixtureManager::prepareTable('', '');
    }

    protected function tearDown ()
    {        
        FixtureManager::prepareTable('rollups', 'totals');
    }    

    public function testCache ()
    {
        $totals = new Totals();
        $totals->create([
            'Totals' => [
                'location_id' => 123,
                'start_date'  => '2014-01-01',
                'end_date'    => '2014-01-01'
            ]
        ]);
        $result = $totals->getFromRaw();
        $this->assertEquals($result['walkbys'], 0);
        
        $totals->updateRollupMetric('2014-01-01', 123, 'walkbys', 100);
        $result = $totals->getFromRaw();
        $this->assertEquals($result['walkbys'], 100);
        
        $totals->updateRollupMetric('2014-01-01', 123, 'walkbys', 200);
        $result = $totals->getFromRaw();
        $this->assertEquals($result['walkbys'], 200);                
    }   

}
