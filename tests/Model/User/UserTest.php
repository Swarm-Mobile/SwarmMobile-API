<?php

class UserTest extends PHPUnit_Framework_TestCase
{

    protected function setUp ()
    {
        //FixtureManager::prepareTable('', '');
    }

    protected function tearDown ()
    {
        //FixtureManager::prepareTable('', '');
    }

    public function testAuthenticate ()
    {
        $user = new User();
        $this->assertFalse($user->authenticate('whatever', 'asdfg'));
        $this->assertFalse($user->authenticate('admin', '123'));
        $user = $user->authenticate('admin', 'asdfg');
        $expected = ['id', 'uuid', 'username', 'usertype_id', 'email'];                
        $this->assertEmpty(array_diff($expected, array_keys($user)));
    }

    public function testHashPassword ()
    {
        $user = new User();
        $this->assertFalse($user->hash_password(false));
        $this->assertNotEmpty($user->hash_password('asbdf'));
        $this->assertNotEmpty($user->hash_password('asbdf', '2321312', 128));
        try {
            $this->assertNotEmpty($user->hash_password('asbdf', false, 123));
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testVerifyEmail ()
    {
        $user = new User();
        $this->assertFalse($user->verifyEmail(false));
        $this->assertFalse($user->verifyEmail('stevebeyatte@gmail'));
        $this->assertNotEmpty($user->verifyEmail('stevebeyatte@gmail.com'));
    }

    public function testCheckEmailExists ()
    {
        $user       = new User();
        $this->assertTrue($user->checkEmailExists('stevebeyatte@gmail.com', 1));
        $user->data = ['User' => ['id' => 1]];
        $this->assertTrue($user->checkEmailExists('stevebeyatte@gmail.com', ['User' => ['id' => 1]]));
        try {
            $this->assertTrue($user->checkEmailExists('stevebeyatte@gmail.com', 'whatever'));
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }
        $this->assertFalse($user->checkEmailExists('stevebeyatte@gmail.com', 0));
    }

    public function testCheckUsernameExists ()
    {
        $user       = new User();
        $this->assertTrue($user->checkUsernameExists('admin', 1));
        $user->data = ['User' => ['id' => 1]];
        $this->assertTrue($user->checkUsernameExists('whatever', ['User' => ['id' => 1]]));
        try {
            $this->assertTrue($user->checkUsernameExists('admin', 'whatever'));
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }
        $this->assertFalse($user->checkUsernameExists('admin', 0));
    }

    public function testValidateConfirmPassword ()
    {
        $user       = new User();
        $user->data = [
            'User' => [
                'password'        => 'hola',
                'confirmPassword' => 'hola',
        ]];
        $this->assertTrue($user->validateConfirmPassword(['confirmPassword' => true], 'password'));
    }

    public function testGetLocationList ()
    {
        $user = new User();
        $this->assertEmpty($user->getLocationList());
        $user->read(null, 1);
        $this->assertEquals(0, count($user->getLocationList()));
        $user->read(null, 13);
        $this->assertEquals(0, count($user->getLocationList()));
        $user->read(null, 33);
        $this->assertEquals(1, count($user->getLocationList()));
        $user->read(null, 417);
        $this->assertEquals(0, count($user->getLocationList()));
        $user->read(null, 1477);
        $this->assertEquals(1, count($user->getLocationList()));
        $user->read(null, 1759);
        $this->assertEquals(1, count($user->getLocationList()));
    }

}
