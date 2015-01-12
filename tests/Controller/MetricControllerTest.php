<?php

class MetricControllerTest extends PHPUnit_Framework_TestCase
{

    protected function setUp ()
    {
        //FixtureManager::prepareTable('', '');
    }

    protected function tearDown ()
    {
        //FixtureManager::prepareTable('', '');
    }

    public function testDevicesModel ()
    {
        $metricController = new MetricController();
        $this->assertEquals(new Devices(), $metricController->getDevices());
        $metricController->setDevices(new Devices(1));
        $this->assertEquals(new Devices(1), $metricController->getDevices());
    }

    public function testPortalTrafficModel ()
    {
        $metricController = new MetricController();
        $this->assertEquals(new PortalTraffic(), $metricController->getPortalTraffic());
        $metricController->setPortalTraffic(new PortalTraffic(1));
        $this->assertEquals(new PortalTraffic(1), $metricController->getPortalTraffic());
    }

    public function testPresenceReturningByDateModel ()
    {
        $metricController = new MetricController();
        $this->assertEquals(new PresenceReturningByDate(), $metricController->getPresenceReturningByDate());
        $metricController->setPresenceReturningByDate(new PresenceReturningByDate(1));
        $this->assertEquals(new PresenceReturningByDate(1), $metricController->getPresenceReturningByDate());
    }

    public function testPresenceReturningByHourModel ()
    {
        $metricController = new MetricController();
        $this->assertEquals(new PresenceReturningByHour(), $metricController->getPresenceReturningByHour());
        $metricController->setPresenceReturningByHour(new PresenceReturningByHour(1));
        $this->assertEquals(new PresenceReturningByHour(1), $metricController->getPresenceReturningByHour());
    }

    public function testPresenceTrafficByDateModel ()
    {
        $metricController = new MetricController();
        $this->assertEquals(new PresenceTrafficByDate(), $metricController->getPresenceTrafficByDate());
        $metricController->setPresenceTrafficByDate(new PresenceTrafficByDate(1));
        $this->assertEquals(new PresenceTrafficByDate(1), $metricController->getPresenceTrafficByDate());
    }

    public function testPresenceTrafficByHourModel ()
    {
        $metricController = new MetricController();
        $this->assertEquals(new PresenceTrafficByHour(), $metricController->getPresenceTrafficByHour());
        $metricController->setPresenceTrafficByHour(new PresenceTrafficByHour(1));
        $this->assertEquals(new PresenceTrafficByHour(1), $metricController->getPresenceTrafficByHour());
    }

    public function testRevenueModel ()
    {
        $metricController = new MetricController();
        $this->assertEquals(new Revenue(), $metricController->getRevenue());
        $metricController->setRevenue(new Revenue(1));
        $this->assertEquals(new Revenue(1), $metricController->getRevenue());
    }

    public function testTimeInShopModel ()
    {
        $metricController = new MetricController();
        $this->assertEquals(new TimeInShop(), $metricController->getTimeInShop());
        $metricController->setTimeInShop(new TimeInShop(1));
        $this->assertEquals(new TimeInShop(1), $metricController->getTimeInShop());
    }

    public function testTotalItemsModel ()
    {
        $metricController = new MetricController();
        $this->assertEquals(new TotalItems(), $metricController->getTotalItems());
        $metricController->setTotalItems(new TotalItems(1));
        $this->assertEquals(new TotalItems(1), $metricController->getTotalItems());
    }

    public function testTotalsModel ()
    {
        $metricController = new MetricController();
        $this->assertEquals(new Totals(), $metricController->getTotals());
        $metricController->setTotals(new Totals(1));
        $this->assertEquals(new Totals(1), $metricController->getTotals());
    }

    public function testTrafficModel ()
    {
        $metricController = new MetricController();
        $this->assertEquals(new Traffic(), $metricController->getTraffic());
        $metricController->setTraffic(new Traffic(1));
        $this->assertEquals(new Traffic(1), $metricController->getTraffic());
    }

    public function testTransactionsModel ()
    {
        $metricController = new MetricController();
        $this->assertEquals(new Transactions(), $metricController->getTransactions());
        $metricController->setTransactions(new Transactions(1));
        $this->assertEquals(new Transactions(1), $metricController->getTransactions());
    }

    public function testWalkbysModel ()
    {
        $metricController = new MetricController();
        $this->assertEquals(new Walkbys(), $metricController->getWalkbys());
        $metricController->setWalkbys(new Walkbys(1));
        $this->assertEquals(new Walkbys(1), $metricController->getWalkbys());
    }

    public function testFootTrafficModel ()
    {
        $metricController = new MetricController();
        $this->assertInstanceOf('FootTraffic', $metricController->getFootTraffic());

        $metricController->setFootTraffic(new PortalTraffic(1));
        $this->assertInstanceOf('FootTraffic', $metricController->getFootTraffic());
        $this->assertEquals(new PortalTraffic(1), $metricController->getFootTraffic());

        $metricController->setFootTraffic(new PresenceTraffic(1));
        $this->assertInstanceOf('FootTraffic', $metricController->getFootTraffic());
        $this->assertEquals(new PresenceTraffic(1), $metricController->getFootTraffic());

        $metricController = new MetricController();
        $this->assertInstanceOf('FootTraffic', $metricController->getFootTraffic(['location_id' => 689]));
    }

    public function testReturningModel ()
    {
        $metricController = new MetricController();
        $this->assertInstanceOf('Returning', $metricController->getReturning());

        $metricController->setReturning(new PresenceReturning(1));
        $this->assertInstanceOf('Returning', $metricController->getReturning());
        $this->assertEquals(new PresenceReturning(1), $metricController->getReturning());
    }

    public function testMonthlyTotalsModel ()
    {
        $metricController = new MetricController();
        $this->assertEquals(new MonthlyTotals(), $metricController->getMonthlyTotals());
        $metricController->setMonthlyTotals(new MonthlyTotals(1));
        $this->assertEquals(new MonthlyTotals(1), $metricController->getMonthlyTotals());
    }

    public function testHistoricalTotalsModel ()
    {
        $metricController = new MetricController();
        $this->assertEquals(new HistoricalTotals(), $metricController->getHistoricalTotals());
        $metricController->setHistoricalTotals(new HistoricalTotals(1));
        $this->assertEquals(new HistoricalTotals(1), $metricController->getHistoricalTotals());
    }

    private function validateHourlyBreakdownResultFormat ($result, $params)
    {
        $expected = ['data', 'options'];
        $this->assertEmpty(array_diff($expected, array_keys($result)));

        $expected = ['start_date', 'end_date', 'location_id', 'endpoint'];
        $this->assertEmpty(array_diff($expected, array_keys($result['options'])));

        $expected = ['breakdown', 'totals'];
        $this->assertEmpty(array_diff($expected, array_keys($result['data'])));

        $expected = ['open', 'close', 'total'];
        $this->assertEmpty(array_diff($expected, array_keys($result['data']['totals'])));

        $start = new DateTime($params['start_date']);
        $end   = new DateTime($params['end_date']);
        while ($start <= $end) {
            $cDate = $start->format('Y-m-d');
            $this->assertArrayHasKey($cDate, $result['data']['breakdown']);

            $expected = ['hours', 'totals'];
            $this->assertEmpty(array_diff($expected, array_keys($result['data']['breakdown'][$cDate])));

            $expected = ['open', 'close', 'total'];
            $this->assertEmpty(array_diff($expected, array_keys($result['data']['breakdown'][$cDate]['totals'])));

            $this->assertEquals(count($result['data']['breakdown'][$cDate]['hours']), 24);
            for ($i = 0; $i < 24; $i++) {
                $h        = $i < 10 ? '0' . $i : (string) $i;
                $this->assertArrayHasKey($h, $result['data']['breakdown'][$cDate]['hours']);
                $expected = ['open', 'total'];
                $this->assertEmpty(array_diff($expected, array_keys($result['data']['breakdown'][$cDate]['hours'][$h])));
            }
            date_add($start, date_interval_create_from_date_string('+1 days'));
        }
    }

    public function testNighclubSwitch ()
    {
        $query          = [
            'location_id' => 1494,
            'start_date'  => '2014-01-01',
            'end_date'    => '2014-01-02'
        ];
        $request        = new CakeRequest('/location/avgTicket');
        $request->query = $query;

        $metricController = new MetricController($request);
        $response         = $metricController->avgTicket();
        $this->assertInstanceOf('JsonResponse', $response);
        $this->validateHourlyBreakdownResultFormat(json_decode($response->body(), true), $query);

        $metricController = new MetricController(new CakeRequest());
        try {
            $metricController->avgTicket();
            $this->assertTrue(false);
        }
        catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testExceptions ()
    {
        $request                   = new CakeRequest('');
        $metricController          = new MetricController();
        $metricController->request = &$request;
        $request->query            = [];
        $metrics                   = [
            'avgTicket', 'conversionRate', 'dwell',
            'footTraffic', 'itemsPerTransaction', 'totalItems',
            'transactions', 'revenue', 'returning',
            'windowConversion', 'portalTraffic', 'totals',
            'monthlyTotals', 'historicalTotals', 'walkbys',
        ];
        foreach ($metrics as $metric) {
            try {
                $metricController->$metric();
                $this->assertTrue(false);
            }
            catch (Swarm\RequestValidationException $e) {
                $this->assertEquals(SwarmErrorCodes::VALIDATION_ERROR, $e->getCode());
            }
        }
    }

    public function testDashboardMetrics ()
    {
        $request                   = new CakeRequest('');
        $metricController          = new MetricController();
        $metricController->request = &$request;
        $request->query            = [
            'location_id' => 689,
            'start_date'  => '2014-01-01',
            'end_date'    => '2014-01-02'
        ];
        $metrics                   = [
            'avgTicket', 'conversionRate', 'dwell',
            'footTraffic', 'itemsPerTransaction', 'totalItems',
            'transactions', 'revenue', 'returning',
            'windowConversion', 'portalTraffic', 'walkbys',
        ];
        foreach ($metrics as $metric) {
            $response = $metricController->$metric();
            $this->assertInstanceOf('JsonResponse', $response);
            $this->validateHourlyBreakdownResultFormat(json_decode($response->body(), true), $request->query);
        }
    }

    public function testTotals ()
    {
        $request        = new CakeRequest('/location/totals');
        $request->query = [
            'location_id' => 689,
            'start_date'  => '2014-01-01',
            'end_date'    => '2014-01-02'
        ];

        $metricController = new MetricController($request);
        $response         = $metricController->totals();
        $this->assertInstanceOf('JsonResponse', $response);
        $this->assertTrue($response->statusCode() == 200);
    }

    public function testMonthlyTotals ()
    {
        for ($i = 1; $i <= 12; $i++) {
            $query          = [
                'location_id' => 689,
                'month'       => $i < 10 ? '0' . $i : (string) $i,
                'year'        => '2014'
            ];
            $request        = new CakeRequest('/location/monthlyTotals');
            $request->query = $query;

            $metricController = new MetricController($request);
            $response         = $metricController->monthlyTotals();
            $this->assertInstanceOf('JsonResponse', $response);
            $this->assertTrue($response->statusCode() == 200);
        }
    }

    public function testHistoricalTotals ()
    {
        $request        = new CakeRequest('/location/historicalTotals');
        $request->query = ['location_id' => 689];

        $metricController = new MetricController($request);
        $response         = $metricController->historicalTotals();
        $this->assertInstanceOf('JsonResponse', $response);
        $this->assertTrue($response->statusCode() == 200);
    }

}
