<?php

class CustomerTest extends PHPUnit_Framework_TestCase
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
        $customer = new Customer();
        try {
            $customer->search('wrong', [], 'last_seen', 25, 0, 'America/Los_Angeles');
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }
        try {
            $customer->search(689, [], 'last_seen', 'wrong', 0, 'America/Los_Angeles');
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }
        try {
            $customer->search(689, [], 'last_seen', 25, 'wrong', 'America/Los_Angeles');
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }
        try {
            $customer->search(689, [], 'last_seen', 25, 0, 'wrong');
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }

        $filters = [
            'outlet',
            'register',
            'visit',
            'sku',
            'hasEmail',
            'transaction',
            'amount',
            'start_date',
            'end_date',
        ];

        foreach ($filters as $filter) {
            try {
                $customer->search(689, [$filter => 'wrong'], 'last_seen', 25, 0, 'America/Los_Angeles');
                $this->assertTrue(false);
            }
            catch (Exception $e) {
                $this->assertTrue(true);
            }
        }
    }

    public function testSearch ()
    {
        $filters   = [
            'outlet'      => 80,
            'register'    => 1,
            'visit'       => 1,
            'sku'         => '1234567890',
            'category'    => 'Category',
            'hasEmail'    => true,
            'transaction' => 1,
            'amount'      => 100.05,
            'brand'       => 'Brand',
            'start_date'  => '2014-01-01',
            'end_date'    => '2014-01-31',
        ];
        $customer  = new Customer();
        $customers = $customer->search(704, $filters);
        $this->assertEquals(count($customers), 0);

        $customers = $customer->search(704);
        $this->assertEquals(count($customers), 25);

        $customers = $customer->search(704, ['amount' => 80.23]);
        foreach ($customers as $customer) {
            $this->assertGreaterThanOrEqual(80.23, $customer[0]['amount']);
        }
    }

}
