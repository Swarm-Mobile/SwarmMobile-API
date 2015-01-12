<?php

class DeviceStatusTest extends PHPUnit_Framework_TestCase
{

    protected function tearDown ()
    {        
        FixtureManager::prepareTable('swarm_backstage', 'devicestatus');        
    }

    public function testRecovery ()
    {
        $deviceStatus = new DeviceStatus();        
        $count             = $deviceStatus->find('count');
        $all               = $deviceStatus->find('all');
        $first             = $deviceStatus->find('first');
        $this->assertEquals($count, 6);
        $this->assertEquals($all[0], $first);
        $expected = ['id', 'name', 'description'];                        
        $this->assertEmpty(array_diff($expected, array_keys($first['DeviceStatus'])));
    }

    public function testCreate ()
    {
        $probes = [
            ['data' => ['name' => 'test', 'description' => '1232131'], 'result' => true],
            ['data' => ['name' => 'test'], 'result' => true],
            ['data' => [], 'result' => false],            
        ];
        foreach ($probes as $k => $probe) {
            $deviceStatus = new DeviceStatus();
            $deviceStatus->create(['DeviceStatus' => $probe['data']]);
            $this->assertEquals(
                $deviceStatus->validates(), 
                $probe['result'], 
                'Probe #' . $k . ' ' . json_encode($deviceStatus->validationErrors)
            );
        }
    }

    public function testSave ()
    {
        $data              = ['name' => 'test', 'description' => '1232131'];
        $deviceStatus = new DeviceStatus();
        $count             = $deviceStatus->find('count');
        $deviceStatus->create(['DeviceStatus' => $data]);
        $deviceStatus->save();
        $this->assertEquals($count + 1, $deviceStatus->find('count'));
    }

}
