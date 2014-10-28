<?php

/**
 * 
 * Factory Singleton to create Redis Clients into the FW
 */
class RedisComponent
{
    /**
     * Contains the Singleton instances of Redis
     * @var array
     */
    private static $instances = [];

    /**
     * Returns a Singleton instance of a Redis adaptor.
     * @param String $name
     * @return Predis\Client
     * @throws Exception
     */
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