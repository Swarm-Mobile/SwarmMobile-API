<?php

class CustomerControllerTest extends PHPUnit_Framework_TestCase
{

    public function testCustomerModel ()
    {
        $customerController = new CustomerController();
        $this->assertEquals(new Customer(), $customerController->getCustomer());
        $customerController->setCustomer(new Customer(1));
        $this->assertEquals(new Customer(1), $customerController->getCustomer());
    }

    public function testLocationModel ()
    {
        $customerController = new CustomerController();
        $this->assertEquals(new Location(), $customerController->getLocation());
        $customerController->setLocation(new Location(1));
        $this->assertEquals(new Location(1), $customerController->getLocation());
    }

    public function testInvoiceModel ()
    {
        $customerController = new CustomerController();
        $this->assertEquals(new Invoice(), $customerController->getInvoice());
        $customerController->setInvoice(new Invoice(1));
        $this->assertEquals(new Invoice(1), $customerController->getInvoice());
    }

    public function testInvoiceLineModel ()
    {
        $customerController = new CustomerController();
        $this->assertEquals(new InvoiceLine(), $customerController->getInvoiceLine());
        $customerController->setInvoiceLine(new InvoiceLine(1));
        $this->assertEquals(new InvoiceLine(1), $customerController->getInvoiceLine());
    }

    public function testLocationSettingModel ()
    {
        $customerController = new CustomerController();
        $this->assertEquals(new LocationSetting(), $customerController->getLocationSetting());
        $customerController->setLocationSetting(new LocationSetting(1));
        $this->assertEquals(new LocationSetting(1), $customerController->getLocationSetting());
    }

    public function testCustomerExceptions ()
    {
        $request                     = new CakeRequest('/customer');
        $customerController          = new CustomerController();
        $customerController->request = &$request;

        $request->query = [];
        try {
            $customerController->customer();
            $this->assertTrue(false);
        }
        catch (Swarm\RequestValidationException $e) {
            $this->assertEquals(SwarmErrorCodes::VALIDATION_ERROR, $e->getCode());
        }

        $request->query = [
            'customer_id' => 3718753,
            'location_id' => 553
        ];
        try {
            $customerController->customer();
            $this->assertTrue(false);
        }
        catch (Swarm\UnprocessableEntityException $e) {
            $this->assertEquals(SwarmErrorCodes::CUSTOMER_CUSTOMER_INVALID_CUSTOMER, $e->getCode());
        }
    }

    public function testCustomer ()
    {
        $request            = new CakeRequest();
        $request->query     = [
            'customer_id' => 3718755,
            'location_id' => 689
        ];
        $customerController = new CustomerController($request);
        $response           = $customerController->customer();
        $result             = json_decode($response->body(), true);
        $expected           = [
            'id',
            'pos_customer_id',
            'fullname',
            'phone',
            'email',
            'address',
            'city',
            'state',
            'country',
            'transactions'
        ];
        $this->assertEmpty(array_diff($expected, array_keys($result)));
        foreach ($result['transactions'] as $transaction) {
            $expected = ['date', 'total', 'items', 'lines'];
            $this->assertEmpty(array_diff($expected, array_keys($transaction)));
            foreach ($transaction['lines'] as $line) {
                $expected = ['description', 'quantity', 'price'];
                $this->assertEmpty(array_diff($expected, array_keys($line)));
            }
        }
    }

    public function testCustomersExceptions ()
    {
        $request                     = new CakeRequest('/customers');
        $customerController          = new CustomerController();
        $customerController->request = &$request;

        $this->setExpectedException('Swarm\RequestValidationException', null, SwarmErrorCodes::VALIDATION_ERROR);
        $request->query = [];
        $customerController->customer();
        $this->assertTrue(true);

        $this->setExpectedException('Swarm\UnprocessableEntityException', null, SwarmErrorCodes::CUSTOMER_CUSTOMERS_STORE_NOTFOUND);
        $request->query = ['location_id' => 123];
        $customerController->customer();
        $this->assertTrue(true);
    }

    public function testCustomers ()
    {
        $request            = new CakeRequest();
        $request->query     = ['location_id' => 689];
        $customerController = new CustomerController($request);
        $response           = $customerController->customers();
        $result             = json_decode($response->body(), true);
        foreach ($result as $row) {
            $expected = [
                'id',
                'pos_customer_id',
                'fullname',
                'email',
                'transactions',
                'amount',
                'last_seen'
            ];
            $this->assertEquals($expected, array_keys($row));
        }
    }

}
