<?php

class InvoiceLineTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $invoiceLine = new InvoiceLine();
        $this->assertInstanceOf('InvoiceLine', $invoiceLine);
    }

}
