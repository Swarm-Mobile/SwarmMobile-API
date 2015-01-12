<?php

class DatabaseConfigShellTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $databaseConfigShell = new DatabaseConfigShell();
        $this->assertInstanceOf('DatabaseConfigShell', $databaseConfigShell);
    }

}
