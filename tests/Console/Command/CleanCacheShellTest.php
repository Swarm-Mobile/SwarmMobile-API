<?php

class CleanCacheShellTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $cleanCacheShell= new CleanCacheShell();
        $this->assertInstanceOf('CleanCacheShell', $cleanCacheShell);
    }

}
