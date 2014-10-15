<?php

class RedisComponent
{

    private static $instances = [];

    public static function getInstance ($name)
    {
        if (!isset(self::$instances[$name])) {
            $config = include(APP . '/Config/redis.php');
            if (isset($config[$name])) {
                self::$instances['name'] = new Predis\Client($config[$name]['host']);
            }
            else {
                throw new Exception('Redis Adaptor not found.');
            }
        }
        return self::$instances['name'];
    }

}
