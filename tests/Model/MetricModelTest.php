<?php

class MetricModelTest extends PHPUnit_Framework_TestCase
{

    public function testGettersAndSetters ()
    {
        $metricModel = $this->getMockForAbstractClass('MetricModel');

        $this->assertEquals($metricModel->getEndDate(), null);
        $metricModel->setEndDate('2014-01-01');
        $this->assertEquals($metricModel->getEndDate(), '2014-01-01');

        $this->assertEquals($metricModel->getEndTime(), null);
        $metricModel->setEndTime('2014-01-01 01:01:01');
        $this->assertEquals($metricModel->getEndTime(), '2014-01-01 01:01:01');

        $this->assertEquals($metricModel->getLocationId(), null);
        $metricModel->setLocationId(689);
        $this->assertEquals($metricModel->getLocationId(), 689);

        $this->assertEquals($metricModel->getLocationSetting(), null);
        $metricModel->setLocationSetting(new LocationSetting());
        $this->assertInstanceOf('LocationSetting', $metricModel->getLocationSetting());

        $this->assertEquals($metricModel->getStartDate(), null);
        $metricModel->setStartDate('2014-01-01');
        $this->assertEquals($metricModel->getStartDate(), '2014-01-01');

        $this->assertEquals($metricModel->getStartTime(), null);
        $metricModel->setStartTime('2014-01-01 01:01:01');
        $this->assertEquals($metricModel->getStartTime(), '2014-01-01 01:01:01');

        $this->assertEquals($metricModel->getTimezone(), null);
        $metricModel->setTimezone('Europe/Dublin');
        $this->assertEquals($metricModel->getTimezone(), 'Europe/Dublin');
        $metricModel->setTimezone('Europe/Dubl');
        $this->assertEquals($metricModel->getTimezone(), 'America/Los_Angeles');
    }

    public function testExceptions ()
    {
        $metricModel = $this->getMockForAbstractClass('MetricModel');
        $methods     = [
            'setEndDate',
            'setEndTime',
            'setLocationId',            
            'setStartDate',
            'setStartTime',
        ];
        foreach ($methods as $method) {
            try {
                $metricModel->$method("that's weird");
                $this->assertEquals(false, $method);
            }
            catch (InvalidArgumentException $e) {
                $this->assertTrue(true);
            }
        }
    }

    public function testCreate ()
    {
        $probes = [
            ['data' => ['location_id' => 689, 'start_date' => '2014-01-01', 'end_date' => '2014-01-01'], 'result' => true],
            ['data' => ['location_id' => 689, 'start_date' => '2014-01-01'], 'result' => false],
            ['data' => ['location_id' => 689], 'result' => false],
            ['data' => [], 'result' => false],
            ['data' => ['location_id' => 'whatever', 'start_date' => '2014-01-01', 'end_date' => '2014-01-01'], 'result' => false],
            ['data' => ['location_id' => 689, 'start_date' => '2014-01', 'end_date' => '2014-01-01'], 'result' => false],
            ['data' => ['location_id' => 689, 'start_date' => '2014-01-01', 'end_date' => '2014-01-'], 'result' => false],
        ];
        foreach ($probes as $k => $probe) {
            $metricModel = $this->getMockForAbstractClass('MetricModel');
            $metricModel->create($probe['data']);
            $this->assertEquals($metricModel->validates(), $probe['result']);
        }
    }

    public function testNeedsArchive ()
    {
        $metricModel = $this->getMockForAbstractClass('MetricModel');
        $metricModel->setStartTime('2014-01-01 00:00:00');
        $metricModel->setEndTime('2014-01-01 00:00:00');
        $this->assertTrue($metricModel->needsSessionTable('sessions_archive'));

        $metricModel->setStartTime(date('Y-m-d H:i:s'));
        $metricModel->setEndTime(date('Y-m-d H:i:s'));
        $this->assertFalse($metricModel->needsSessionTable('sessions_archive'));
    }

}
