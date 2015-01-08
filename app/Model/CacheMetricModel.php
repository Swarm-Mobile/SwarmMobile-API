<?php

App::uses('MetricModel', 'Model');

abstract class CacheMetricModel extends MetricModel
{

    abstract function getFromCache ();

    abstract function storeInCache ($result = []);
    
}
