<?php

App::uses('RedisComponent', 'Controller/Component');
App::uses('AppShell', 'Console/Command');
App::uses('PingSession', 'Model');
App::uses('PingFootprint', 'Model');

class PingAsPresenceShell extends AppShell
{
    /**
     * Redis connection object
     *  
     */
    public $redis = NULL;

    public function main ()
    {
        $this->setEnvironment();
        $this->setRedis();
        $this->out('Reading data from redis.');
        $redisKeys = $this->redis->keys('*');
        foreach($redisKeys as $hKey) {
            $this->processUserSession($hKey);
            $this->redis->del($hKey);
        }
    }

    /**
     * Set the redis connection object 
     *
     * @var String redis config name
     */
    private function setRedis($name = 'pingAsPresence')
    {
        if ($this->redis === NULL) {
            try {
                $this->redis = RedisComponent::getInstance($name);
            } catch (Exception $e) {
                die("Cannot connect to Redis\n");
            }
        }
    }

    /**
     * Process user sessions and footprints 
     * 
     * @var Array session data
     */
    private function processUserSession($hKey)
    {
        if (empty($hKey)) return;

        $data = $this->redis->hgetall($hKey);
        $setData['PingSession'] = [];
        list($setData['PingSession']['location_id'], $setData['PingSession']['user_id']) = split(':', $hKey);
        $sessionObj = new PingSession();
        // Looping throuh all the hash keys
        foreach ($data as $key => $value) {
            // Check if the key is the deviceId
            if (preg_match('/device:(\d+)(-\d+)?/', $key, $matches)) {
                $offset = (!empty($matches[2])) ? $matches[2] : '';
                $rows = split('\$', $value);
                if(!empty($rows)) {
                    foreach ($rows as $row) {
                        $footprint = [];
                        $fields = split(':', $row);
                        if(is_numeric($fields[0]) && (int) $fields[0] == $fields[0]) {
                            $date = new DateTime('@' . $fields[0]);
                            $dateTime  = $date->format('Y-m-d H:i:s');
                            if (empty($setData['PingSession']['ts_start'])) {
                                $setData['PingSession']['ts_start'] = $dateTime;
                                $setData['PingSession']['ts_end']   = $dateTime;
                            } else {
                                $timeDiff = round(abs(strtotime($dateTime) - strtotime($setData['PingSession']['ts_end'])) / 60, 2);
                                if ($timeDiff > 0) {
                                    $setData['PingSession']['ts_end'] = $dateTime;
                                }
                            }
                            $footprint['device_id']   = $matches[1];
                            $footprint['latitude']    = $fields[1];
                            $footprint['longitude']   = $fields[2];
                            $footprint['rssi']        = $fields[3];
                            $footprint['ts_creation'] = $dateTime;
                            $setData['PingFootprint'][]   = $footprint;
                        }
                    }
                    $sessionObj->saveAssociated($setData);
                    $this->out("SessionId $sessionObj->id added.");
                    $sessionObj->clear();
                    unset($setData['PingSession']['ts_start']);
                    unset($setData['PingSession']['ts_end']);
                }
            }
        }
    }
}