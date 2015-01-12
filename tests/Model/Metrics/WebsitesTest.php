<?php

class WebsitesTest extends PHPUnit_Framework_TestCase
{

    protected function setUp ()
    {
        //FixtureManager::prepareTable('', '');
    }

    protected function tearDown ()
    {
        //FixtureManager::prepareTable('', '');
    }

    public function testExceptions ()
    {
        $websites = new Websites();
        try {
            $websites->getFromRaw();
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }

        //Location without Network ID
        $websites->create([
            'Websites' => [
                'location_id' => 123,
                'start_date'  => '2014-10-01',
                'end_date'    => '2014-10-30',
            ]], false);

        try {
            $websites->getFromRaw();
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testGetFromRaw ()
    {
        $websites = new Websites();
        $websites->create([
            'Websites' => [
                'location_id' => 1494,
                'start_date'  => '2014-10-01',
                'end_date'    => '2014-10-30',
            ]], false);
        $result   = $websites->getFromRaw();
        foreach ($result as $row) {
            $this->assertEquals(['domain','count'], array_keys($row));
        }
    }

}
