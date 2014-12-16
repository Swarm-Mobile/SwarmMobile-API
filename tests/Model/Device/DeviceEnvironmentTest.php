<?php

class DeviceEnvironmentTest extends PHPUnit_Framework_TestCase
{

    protected function setUp ()
    {
        //FixtureManager::prepareTable('', '');
    }

    protected function tearDown ()
    {
        //FixtureManager::prepareTable('', '');
    }

    public function testRecovery ()
    {
        $deviceEnvironment = new DeviceEnvironment();
        $count             = $deviceEnvironment->find('count');
        $all               = $deviceEnvironment->find('all');
        $first             = $deviceEnvironment->find('first');
        $this->assertEquals($count, 2);
        $this->assertEquals($all[0], $first);
        $expected = ['id', 'uuid', 'name'];        
        $this->assertArrayHasKey('DeviceEnvironment', $first);        
        $this->assertEmpty(array_diff($expected, array_keys($first['DeviceEnvironment'])));
    }

    public function testCreate ()
    {
        $probes = [
            ['data' => ['name' => 'test', 'uuid' => '1232131'], 'result' => true],
            ['data' => ['name' => 'test', 'uuid' => '1232131', 'hello' => 'foo'], 'result' => true],
            ['data' => ['uuid' => '1232131'], 'result' => false],
            ['data' => ['name' => 'test'], 'result' => false],
        ];
        foreach ($probes as $k => $probe) {
            $deviceEnvironment = new DeviceEnvironment();
            $deviceEnvironment->create(['DeviceEnvironment' => $probe['data']]);
            $this->assertEquals(
                $deviceEnvironment->validates(), 
                $probe['result'], 
                'Probe #' . $k . ' ' . json_encode($deviceEnvironment->validationErrors)
            );
        }
    }

    public function testSave ()
    {
        $data              = ['name' => 'test', 'uuid' => '1232131'];
        $deviceEnvironment = new DeviceEnvironment();
        $count             = $deviceEnvironment->find('count');
        $deviceEnvironment->create(['DeviceEnvironment' => $data]);
        $deviceEnvironment->save();
        $this->assertEquals($count + 1, $deviceEnvironment->find('count'));
        FixtureManager::prepareTable('swarm_backstage', 'deviceenvironment');
    }

}
