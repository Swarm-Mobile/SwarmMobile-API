<?php

class BrandControllerTest extends PHPUnit_Framework_TestCase
{

    protected function setUp ()
    {
        //FixtureManager::prepareTable('', '');
    }

    protected function tearDown ()
    {
        //FixtureManager::prepareTable('', '');
    }

    public function testLocationModel ()
    {
        $brandController = new BrandController();
        $this->assertEquals(new Location(), $brandController->getLocation());
        $brandController->setLocation(new Location(1));
        $this->assertEquals(new Location(1), $brandController->getLocation());
    }

    public function testLocationSettingModel ()
    {
        $brandController = new BrandController();
        $this->assertEquals(new LocationSetting(), $brandController->getLocationSetting());
        $brandController->setLocationSetting(new LocationSetting(1));
        $this->assertEquals(new LocationSetting(1), $brandController->getLocationSetting());
    }

    public function testInvoiceLineModel ()
    {
        $brandController = new BrandController();
        $this->assertEquals(new InvoiceLine(), $brandController->getInvoiceLine());
        $brandController->setInvoiceLine(new InvoiceLine(1));
        $this->assertEquals(new InvoiceLine(1), $brandController->getInvoiceLine());
    }

    public function testBrandsExceptions ()
    {
        $request                  = new CakeRequest('/brands');
        $brandController          = new BrandController();
        $brandController->request = &$request;
        
        $request->query = [];
        try {
            $brandController->brands();
            $this->assertTrue(false);
        }
        catch (Swarm\RequestValidationException $e) {
            $this->assertEquals(SwarmErrorCodes::VALIDATION_ERROR, $e->getCode());
        }

        $request->query = ['location_id' => 123];
        try {
            $brandController->brands();
            $this->assertTrue(false);
        }
        catch (Swarm\UnprocessableEntityException $e) {
            $this->assertEquals(SwarmErrorCodes::BRAND_BRANDS_LOCATION_NOTFOUND, $e->getCode());
        }

        $request->query = ['location_id' => 2191];
        try {
            $brandController->brands();
            $this->assertTrue(false);
        }
        catch (Swarm\UnprocessableEntityException $e) {
            $this->assertEquals(SwarmErrorCodes::BRAND_BRANDS_STORE_NOTFOUND, $e->getCode());
        }
    }

    public function testBrands ()
    {
        $request                  = new CakeRequest('/brands');
        $brandController          = new BrandController();
        $brandController->request = &$request;

        $request->query = ['location_id' => 689];
        $response       = $brandController->brands();
        $this->assertEquals(3, count(json_decode($response->body(), true)));
    }

}
