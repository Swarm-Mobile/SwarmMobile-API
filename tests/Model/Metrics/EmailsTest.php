<?php

class EmailsTest extends PHPUnit_Framework_TestCase
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
        $emails = new Emails();
        try {
            $emails->getFromRaw();
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testGetFromRaw ()
    {
        $emails = new Emails();
        $emails->create([
            'Emails' => [
                'location_id' => 1494,
                'start_date'  => '2014-10-01',
                'end_date'    => '2014-10-30',
            ]], false);
        $result = $emails->getFromRaw();
        $sDate  = new DateTime('2014-10-01');
        $eDate  = new DateTime('2014-10-30 23:59:59');
        foreach ($result as $row) {
            $cDate    = new DateTime($row['time']);
            $this->assertGreaterThanOrEqual($sDate, $cDate);
            $this->assertLessThanOrEqual($eDate, $cDate);
            $expected = ['email', 'time'];            
            $this->assertEmpty(array_diff($expected, array_keys($row)));
        }
    }

}
