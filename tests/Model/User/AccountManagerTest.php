<?php

class AccountManagerTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $accountManager = new AccountManager();
        $this->assertInstanceOf('AccountManager', $accountManager);
    }

}
