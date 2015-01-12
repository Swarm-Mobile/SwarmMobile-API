<?php

class PortalControllerTest extends PHPUnit_Framework_TestCase
{

    protected function setUp ()
    {
        //FixtureManager::prepareTable('', '');
    }

    protected function tearDown ()
    {
        FixtureManager::prepareTable('portal', 'visitorEvent');
    }

    public function testVisitorEvent ()
    {
        $request          = new CakeRequest();
        $portalController = new PortalController($request);
        $response         = $portalController->visitorEvent();
        $result           = json_decode($response->body(), true);
        $this->assertEquals(0, $result['count']);

        $request->data    = [
            'upload' => [
                [
                    'serialNumber' => 'demo',
                    'locationID'   => '689',
                    'userID'       => '123',
                    'entered'      => 1,
                    'exited'       => 0,
                    'totalCount'   => 1,
                    'date'         => date('Y-m-d H:i:s')
                ]
            ]
        ];
        $portalController = new PortalController($request);
        $response         = $portalController->visitorEvent();
        $result           = json_decode($response->body(), true);
        $this->assertEquals(1, $result['count']);
    }

}
