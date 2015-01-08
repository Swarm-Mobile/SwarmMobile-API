<?php

App::uses('RedisComponent', 'Controller/Component');
App::uses('CakeEventListener', 'Event');

class RequestCacheListener implements CakeEventListener
{

    public function implementedEvents ()
    {
        return [
            'Dispatcher.beforeDispatch' => [
                'callable' => 'getCache',
                'priority' => '3'
            ],
            'Dispatcher.afterDispatch'  => [
                'callable' => 'setCache',
                'priority' => '3'
            ],
        ];
    }

    public function getCache (CakeEvent $event)
    {
        if ($event->data['request']->is('get')) {
            $params = $event->data['request']->query;
            unset($params['access_token']);
            list($endpoint, ) = explode('?', $_SERVER['REQUEST_URI'] . '?');
            $hash   = hash('sha256', $endpoint . '-' . hash('sha256', json_encode($params)));
            $redis  = RedisComponent::getInstance('APICache');
            if ($redis->exists($hash)) {
                header("HTTP/1.1 200");
                header("Swarm-From-Cache: true");
                header("Access-Control-Allow-Origin: *");
                header("Access-Control-Allow-Methods: POST, GET");
                header("Access-Control-Allow-Headers: X-PINGOTHER");
                header("Content-Type: application/json; charset=UTF-8");
                header("Access-Control-Max-Age: 1728000");
                header("Pragma: no-cache");
                header("Cache-Control: no-store; no-cache;must-revalidate; post-check=0; pre-check=0");
                echo $redis->get($hash);
                exit();
            }
        }
    }

    public function setCache (CakeEvent $event)
    {
        if ($event->data['request']->is('get') && $event->data['response']->statusCode() == '200') {
            $params   = $event->data['request']->query;
            unset($params['access_token']);
            list($endpoint, ) = explode('?', $_SERVER['REQUEST_URI'] . '?');
            $hash     = hash('sha256', $endpoint . '-' . hash('sha256', json_encode($params)));
            $response = $event->data['response']->body();
            $redis    = RedisComponent::getInstance('APICache');
            if (!$redis->exists($hash)) {
                $redis->set($hash, $response);
                $redis->expire($hash, 60);
            }
        }
    }

}
