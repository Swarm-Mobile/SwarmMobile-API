<?php

class SDKControllerTest extends PHPUnit_Framework_TestCase
{

    public function testWhatIsHere ()
    {
        $request                = new CakeRequest('/brands');
        $sdkController          = new SDKController();
        $sdkController->request = &$request;
        $request->data          = [
            'devices' => [
                [
                    'uuid'      => 'E2C56DB5-DFFB-48D2-B060-D0F5A71096E0',
                    'minor'     => 656,
                    'prex'      => 'N',
                    'pr'        => 'I',
                    'longitude' => -122.4031261448957,
                    'rssi'      => -40,
                    'latitude'  => 37.76640390049458,
                    'major'     => 1,
                ]
            ],
            'accuracy'  => 0.0878189980336942
        ];
        $response = $sdkController->whatIsHere();
        $result = json_decode($response->body(),true);
        $this->assertArrayHasKey('location', $result['data'][0]);
    }

}
