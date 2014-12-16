<?php

class CategoryControllerTest extends PHPUnit_Framework_TestCase
{

    public function testCategoryModel ()
    {
        $categoryController = new CategoryController();
        $this->assertEquals(new Category(), $categoryController->getCategory());
        $categoryController->setCategory(new Category(1));
        $this->assertEquals(new Category(1), $categoryController->getCategory());
    }

    public function testLocationModel ()
    {
        $categoryController = new CategoryController();
        $this->assertEquals(new Location(), $categoryController->getLocation());
        $categoryController->setLocation(new Location(1));
        $this->assertEquals(new Location(1), $categoryController->getLocation());
    }

    public function testLocationSettingModel ()
    {
        $categoryController = new CategoryController();
        $this->assertEquals(new LocationSetting(), $categoryController->getLocationSetting());
        $categoryController->setLocationSetting(new LocationSetting(1));
        $this->assertEquals(new LocationSetting(1), $categoryController->getLocationSetting());
    }

    public function testCategoriesExceptions ()
    {
        $request                     = new CakeRequest('/categories');
        $categoryController          = new CategoryController();
        $categoryController->request = &$request;

        $request->query = [];
        try {
            $categoryController->categories();
            $this->assertTrue(false);
        }
        catch (Swarm\RequestValidationException $e) {
            $this->assertEquals(SwarmErrorCodes::VALIDATION_ERROR, $e->getCode());
        }

        $request->query = ['location_id' => 123];
        try {
            $categoryController->categories();
            $this->assertTrue(false);
        }
        catch (Swarm\UnprocessableEntityException $e) {
            $this->assertEquals(SwarmErrorCodes::CATEGORY_CATEGORIES_LOCATION_NOTFOUND, $e->getCode());
        }

        $request->query = ['location_id' => 2238];
        try {
            $categoryController->categories();
            $this->assertTrue(false);
        }
        catch (Swarm\UnprocessableEntityException $e) {
            $this->assertEquals(SwarmErrorCodes::CATEGORY_CATEGORIES_STORE_NOTFOUND, $e->getCode());
        }       
    }

    public function testCategories ()
    {
        $request                     = new CakeRequest('/categories');
        $categoryController          = new CategoryController();
        $categoryController->request = &$request;
        $request->query              = ['location_id' => 689];
        $response                    = $categoryController->categories();
        $result                      = json_decode($response, true);
        $this->assertNotEmpty($result);
    }

}
