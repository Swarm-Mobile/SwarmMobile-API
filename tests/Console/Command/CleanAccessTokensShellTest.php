<?php

class CleanAccessTokensShellTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $cleanAccessTokenShell = new CleanAccessTokensShell();
        $this->assertInstanceOf('CleanAccessTokensShell', $cleanAccessTokenShell);
    }

}
