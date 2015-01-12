<?php

class InvoiceTest extends PHPUnit_Framework_TestCase
{

    public function testExceptions ()
    {
        $invoice = new Invoice();
        try {
            $invoice->biggestTicket(null, null, null, 'whatever');
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }
        try {
            $invoice->bestHour(null, null, null, 'whatever');
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }
        try {
            $invoice->bestDay(null, null, null, 'whatever');
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testBiggestTicket ()
    {
        $invoice       = new Invoice();
        $biggestTicket = $invoice->biggestTicket();
        $expected      = ['amount', 'date'];        
        $this->assertEmpty(array_diff($expected, array_keys($biggestTicket)));

        $biggestTicket = $invoice->biggestTicket(704, '2014-01-01', '2014-01-31');
        $expected      = ['amount', 'date'];
        $this->assertEmpty(array_diff($expected, array_keys($biggestTicket)));

        $biggestTicket = $invoice->biggestTicket(123, '2014-01-01', '2014-01-31');
        $this->assertFalse($biggestTicket);
    }

    public function testBestHour ()
    {
        $invoice  = new Invoice();
        $bestHour = $invoice->bestHour();
        $expected = ['amount', 'date'];        
        $this->assertEmpty(array_diff($expected, array_keys($bestHour)));

        $bestHour = $invoice->bestHour(704, '2014-01-01', '2014-01-31');
        $expected = ['amount', 'date'];
        $this->assertEmpty(array_diff($expected, array_keys($bestHour)));

        $bestHour = $invoice->bestHour(123, '2014-01-01', '2014-01-31');
        $this->assertFalse($bestHour);
    }

    public function testBestDay ()
    {
        $invoice  = new Invoice();
        $bestDay  = $invoice->bestDay();
        $expected = ['amount', 'date'];
        $this->assertEmpty(array_diff($expected, array_keys($bestDay)));

        $bestDay  = $invoice->bestDay(704, '2014-01-01', '2014-01-31');
        $expected = ['amount', 'date'];
        $this->assertEmpty(array_diff($expected, array_keys($bestDay)));

        $bestDay = $invoice->bestDay(123, '2014-01-01', '2014-01-31');
        $this->assertFalse($bestDay);
    }

}
