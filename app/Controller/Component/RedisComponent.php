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
                $instance = new Predis\Client($config[$name]['host']);
                if(isset($config[$name]['database'])) {
                    $instance->select($config[$name]['database']);
                }
                self::$instances['name'] = $instance;
            }
            else {
                throw new Swarm\BadRequestException('Redis Adaptor not found.');
            }
        }
        return self::$instances['name'];
    }
}
