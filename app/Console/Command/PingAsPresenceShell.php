<?php

App::uses('RedisComponent', 'Controller/Component');
App::uses('AppShell', 'Console/Command');
App::uses('PingSession', 'Model/Ping');
App::uses('PingFootprint', 'Model/Ping');

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
        foreach ($redisKeys as $hKey) {
            $this->processUserSession($hKey);
        }
    }

    /**
     * Set the redis connection object
     *
     * @var String redis config name
     */
    private function setRedis ($name = 'pingAsPresence')
    {
        if ($this->redis === NULL) {
            try {
                $this->redis = RedisComponent::getInstance($name);
            }
            catch (Exception $e) {
                die("Cannot connect to Redis\n");
            }
        }
    }

    /**
     * Process user sessions and footprints
     *
     * @var Array session data
     */
    private function processUserSession ($hKey)
    {
        if (empty($hKey) || ($hKey === 'ElastiCacheMasterReplicationTimestamp'))
            return;
        $data                   = $this->redis->hgetall($hKey);
        $setData['PingSession'] = [];
        list($setData['PingSession']['location_id'], $setData['PingSession']['user_id']) = split(':', $hKey);
        $sessionObj             = new PingSession();

        // Check if the session is still active
        $date        = new DateTime('now', new DateTimeZone('GMT'));
        $currentTime = $date->format('Y-m-d H:i:s');
        if (!empty($data['timestamp'])) {
            $timeDiff = round(abs(strtotime($currentTime) - strtotime($data['timestamp'])) / 60, 2);
            if ($timeDiff < 20)
                return;
        }

        // Looping throuh all the hash keys
        foreach ($data as $key => $value) {
            // Check if the key is the deviceId
            if (preg_match('/device:(\d+)(-\d+)?/', $key, $matches)) {
                $offset = (!empty($matches[2])) ? $matches[2] : '';
                $rows   = split('\$', $value);
                if (!empty($rows)) {
                    foreach ($rows as $row) {
                        $footprint = [];
                        $fields    = split(':', $row);
                        if (is_numeric($fields[0]) && (int) $fields[0] == $fields[0]) {
                            $date     = new DateTime('@' . $fields[0]);
                            $dateTime = $date->format('Y-m-d H:i:s');
                            if (empty($setData['PingSession']['ts_start'])) {
                                $setData['PingSession']['ts_start'] = $dateTime;
                                $setData['PingSession']['ts_end']   = $dateTime;
                            }
                            else {
                                $timeDiff = round(abs(strtotime($dateTime) - strtotime($setData['PingSession']['ts_end'])) / 60, 2);
                                if ($timeDiff > 0) {
                                    $setData['PingSession']['ts_end'] = $dateTime;
                                }
                            }
                            $footprint['device_id']     = $matches[1];
                            $footprint['latitude']      = $fields[1];
                            $footprint['longitude']     = $fields[2];
                            $footprint['rssi']          = $fields[3];
                            $footprint['ts_creation']   = $dateTime;
                            $setData['PingFootprint'][] = $footprint;
                        }
                    }
                    $sessionObj->save($setData, true, array_keys($setData['PingSession']));
                    foreach ($setData['PingFootprint'] as $pingFootPrintData) {
                        $footPrint = new PingFootprint();
                        $footPrint->save(['PingFootprint' => $pingFootPrintData], true, array_keys($pingFootPrintData));
                    }
                    $this->out("SessionId $sessionObj->id added.");
                    $sessionObj->clear();
                    unset($setData['PingSession']['ts_start']);
                    unset($setData['PingSession']['ts_end']);
                }
            }
        }
        $this->redis->del($hKey);
    }

}
