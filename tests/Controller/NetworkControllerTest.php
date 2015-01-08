<?php

class NetworkControllerTest extends PHPUnit_Framework_TestCase
{

    public function testWifiConnectionsModel ()
    {
        $networkController = new NetworkController();
        $this->assertEquals(new WifiConnections(), $networkController->getWifiConnections());
        $networkController->setWifiConnections(new WifiConnections(1));
        $this->assertEquals(new WifiConnections(1), $networkController->getWifiConnections());
    }

    public function testEmailsCapturedModel ()
    {
        $networkController = new NetworkController();
        $this->assertEquals(new EmailsCaptured(), $networkController->getEmailsCaptured());
        $networkController->setEmailsCaptured(new EmailsCaptured(1));
        $this->assertEquals(new EmailsCaptured(1), $networkController->getEmailsCaptured());
    }

    public function testEmailsModel ()
    {
        $networkController = new NetworkController();
        $this->assertEquals(new Emails(), $networkController->getEmails());
        $networkController->setEmails(new Emails(1));
        $this->assertEquals(new Emails(1), $networkController->getEmails());
    }

    public function testWebsitesModel ()
    {
        $networkController = new NetworkController();
        $this->assertEquals(new Websites(), $networkController->getWebsites());
        $networkController->setWebsites(new Websites(1));
        $this->assertEquals(new Websites(1), $networkController->getWebsites());
    }

    public function testExceptions ()
    {
        $request                    = new CakeRequest('');
        $networkController          = new NetworkController();
        $networkController->request = &$request;
        $request->query             = [];
        $metrics                    = ['wifiConnections', 'emailsCaptured', 'emails', 'websites'];
        foreach ($metrics as $metric) {            
            try {
                $networkController->$metric();
                $this->assertTrue(false);
            }
            catch (Swarm\RequestValidationException $e) {
                $this->assertEquals(SwarmErrorCodes::VALIDATION_ERROR, $e->getCode());
            }
        }
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

    public function testWifiConnections ()
    {
        $request                    = new CakeRequest();
        $networkController          = new NetworkController();
        $networkController->request = &$request;
        $request->query             = [
            'location_id' => 1494,
            'start_date'  => '2014-10-01',
            'end_date'    => '2014-10-30'
        ];
        $response                   = $networkController->wifiConnections();
        $this->assertInstanceOf('JsonResponse', $response);
        $this->validateHourlyBreakdownResultFormat(json_decode($response->body(), true), $request->query);
    }

    public function testEmailsCaptured ()
    {
        $request                    = new CakeRequest();
        $networkController          = new NetworkController();
        $networkController->request = &$request;
        $request->query             = [
            'location_id' => 1494,
            'start_date'  => '2014-10-01',
            'end_date'    => '2014-10-30'
        ];
        $response                   = $networkController->emailsCaptured();
        $this->assertInstanceOf('JsonResponse', $response);
        $this->validateHourlyBreakdownResultFormat(json_decode($response->body(), true), $request->query);
    }

    public function testEmails ()
    {
        $request                    = new CakeRequest();
        $networkController          = new NetworkController();
        $networkController->request = &$request;
        $request->query             = [
            'location_id' => 1494,
            'start_date'  => '2014-10-01',
            'end_date'    => '2014-10-30'
        ];
        $response                   = $networkController->emails();
        $this->assertInstanceOf('JsonResponse', $response);
        $result                     = json_decode($response->body(), true);
        $this->assertNotEmpty($result);
    }

    public function testWebsites ()
    {
        $request                    = new CakeRequest();
        $networkController          = new NetworkController();
        $networkController->request = &$request;
        $request->query             = [
            'location_id' => 1494,
            'start_date'  => '2014-10-01',
            'end_date'    => '2014-10-30'
        ];
        $response                   = $networkController->websites();
        $this->assertInstanceOf('JsonResponse', $response);
        $result                     = json_decode($response->body(), true);
        $this->assertNotEmpty($result);
    }

}
