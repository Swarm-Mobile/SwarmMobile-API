<?php

class DeviceTypeTest extends PHPUnit_Framework_TestCase
{

    protected function setUp ()
    {
        //FixtureManager::prepareTable('', '');
    }

    protected function tearDown ()
    {
        FixtureManager::prepareTable('swarm_backstage', 'devicetype');
    }

    public function testRecovery ()
    {
        $deviceType = new DeviceType();
        $count      = $deviceType->find('count');
        $all        = $deviceType->find('all');
        $first      = $deviceType->find('first');
        $this->assertEquals($count, 3);
        $this->assertEquals($all[0], $first);
        $expected       = [
            'id',
            'name',
            'description',
            'source',
            'ts_creation',
            'ts_update'
        ];
        $this->assertArrayHasKey('DeviceType', $first);        
        $this->assertEmpty(array_diff($expected, array_keys($first['DeviceType'])));
    }

    public function testCreate ()
    {
        $probes = [
            ['data' => ['name' => 'test', 'description' => '1232131', 'source' => 'new'], 'result' => true],
            ['data' => ['name' => 'test', 'description' => '1232131'], 'result' => false],
            ['data' => ['name' => 'test'], 'result' => false],
            ['data' => [], 'result' => false],
        ];
        foreach ($probes as $k => $probe) {
            $deviceType = new DeviceType();
            $deviceType->create(['DeviceType' => $probe['data']]);
            $this->assertEquals(
                    $deviceType->validates(), $probe['result'], 'Probe #' . $k . ' ' . json_encode($deviceType->validationErrors)
            );
        }
    }

    public function testSave ()
    {
        $data       = ['name' => 'test', 'description' => '1232131', 'source' => 'new'];
        $deviceType = new DeviceType();
        $count      = $deviceType->find('count');
        $deviceType->create(['DeviceType' => $data]);
        $deviceType->save();
        $this->assertEquals($count + 1, $deviceType->find('count'));
    }

}
