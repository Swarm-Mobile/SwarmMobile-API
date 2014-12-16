<?php

class CategoryTest extends PHPUnit_Framework_TestCase
{

    public function testCreate ()
    {
        $category = new Category();
        $this->assertInstanceOf('Category', $category);
    }

}
