<?php

class DeviceTest extends PHPUnit_Framework_TestCase
{

    protected function setUp ()
    {
        //FixtureManager::prepareTable('', '');
    }

    protected function tearDown ()
    {        
        FixtureManager::prepareTable('swarm_backstage', 'device');
    }

    public function testRecovery ()
    {
        $device = new Device();
        $count  = $device->find('count');
        $all    = $device->find('all', ['limit' => 10]);
        $first  = $device->find('first');
        $this->assertEquals($count, 10);
        $this->assertEquals($all[0], $first);
        $expected   = [
            'id',
            'mac',
            'reseller_id',
            'location_id',
            'devicetype_id',
            'ts_creation',
            'ts_update',
            'devicestatus_id',
            'alias',
            'major',
            'minor',
            'deviceenvironment_id',
            'serial',
            'manufacturer_serial',
            'notes',
            'developer_id',
            'battery_level',
            'lat',
            'long',
            'last_sync',
            'store_open',
            'store_close',
            'firmware_version',
            'app_version',
        ];
        $this->assertArrayHasKey('Device', $first);        
        $this->assertEmpty(array_diff($expected, array_keys($first['Device'])));
    }

    public function testCreate ()
    {
        $probes = [
            [
                'data'   => [
                    'alias'                => '12313',
                    'mac'                  => '12313',
                    'devicetype_id'        => 1,
                    'major'                => 1,
                    'minor'                => 1,
                    'deviceenvironment_id' => 1,
                    'serial'               => 1,
                    'manufacturer_serial'  => 1,
                ], 'result' => true
            ],
            [
                'data'   => [
                    'alias'                => '123123',
                    'mac'                  => '123123',
                    'devicetype_id'        => 1,
                    'major'                => 1,
                    'minor'                => 'a',
                    'deviceenvironment_id' => 1,
                    'serial'               => 1,
                    'manufacturer_serial'  => 1,
                ], 'result' => false
            ],
            [
                'data'   => [
                    'devicetype_id'        => 1,
                    'major'                => 1,
                    'minor'                => 1,
                    'deviceenvironment_id' => 1,
                    'serial'               => 1,
                ], 'result' => false
            ],
            [
                'data'   => [
                    'devicetype_id'        => 1,
                    'minor'                => 1,
                    'deviceenvironment_id' => 1,
                    'serial'               => 1,
                ], 'result' => false
            ],
            [
                'data'   => [
                    'devicetype_id'        => 1,
                    'minor'                => 1,
                    'deviceenvironment_id' => 1,
                    'serial'               => 1,
                ], 'result' => false
            ],
        ];
        foreach ($probes as $k => $probe) {
            $device = new Device();
            $device->create(['Device' => $probe['data']]);
            $this->assertEquals(
                $device->validates(), 
                $probe['result'], 
                'Probe #' . $k . ' ' . json_encode($device->validationErrors)
            );
        }
    }

    public function testSave ()
    {
        $data   = [
            'alias'                => 'alias',
            'mac'                  => 1,
            'devicetype_id'        => 1,
            'major'                => 1,
            'minor'                => 1,
            'deviceenvironment_id' => 1,
            'serial'               => 1,
            'manufacturer_serial'  => 1
        ];
        $device = new Device();
        $count  = $device->find('count');
        $device->create(['Device' => $data]);
        $device->save();
        $this->assertEquals($count + 1, $device->find('count'));
    }

    public function testBeforeSave ()
    {
        $probes   = [
            ['data'=>['reseller_id'=> 0, 'location_id'=>0], 'result'=>  DeviceStatus::$INVENTORY],            
            ['data'=>['reseller_id'=> 1, 'location_id'=>0], 'result'=>  DeviceStatus::$RESELLER],
            ['data'=>['location_id'=> 1], 'result'=>  DeviceStatus::$DEPLOYED],
        ];
        foreach($probes as $k=>$probe){
            $device = new Device();
            $device->read(null, 3509);
            $device = $device->save(['Device' => $probe['data']], false, array_keys($probe['data']));                                                
            $this->assertEquals($device['Device']['devicestatus_id'], $probe['result'], 'Probe #'.$k);
        }
    }

}
