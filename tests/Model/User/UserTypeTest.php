<?php

class UserTypeTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $userType = new UserType();
        $this->assertInstanceOf('UserType', $userType);
    }

}