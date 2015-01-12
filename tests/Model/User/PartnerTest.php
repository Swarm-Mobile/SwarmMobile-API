<?php

class PartnerTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $partner = new Partner();
        $this->assertInstanceOf('Partner', $partner);
    }

}
