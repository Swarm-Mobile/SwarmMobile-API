<?php

class AppErrorTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $appError = new AppError();
        $this->assertInstanceOf('AppError', $appError);
    }

}
