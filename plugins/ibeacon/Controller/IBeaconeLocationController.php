<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IBeaconeLocationController
 *
 * @author Zotov Maxim <zotov_mv@groupbwt.com>
 */

App::uses('IBeaconController', 'ibeacon.Controller');
App::uses('RedisComponent', 'ibeacon.Controller/Component');
App::uses('IBeaconDeviceCoordinate', 'ibeacon.Model');
App::uses('User', 'ibeacon.Model/PingAsPresence');

class IBeaconeLocationController extends  IBeaconController {

    /**
     *
     */
    public function whereAmI (){
        $locationModel = new IBeaconLocation();
        $deviceCoordinateModel = new IBeaconDeviceCoordinate();
        $LocationIdentifierList = $this->request->data['locations'];
        $response = array();
        foreach ($LocationIdentifierList as $LocationIdentifier){
            $this->IBeacon->logging('whereAmI',  array_merge($LocationIdentifier,$_GET));
            $locations = $locationModel->findByUUID(
                    $LocationIdentifier['uuid'],
                    $LocationIdentifier['major'],
                    $LocationIdentifier['minor']
            );
            foreach ($locations as $location){
                if(isset($location['IBeaconLocation']) && !empty($location['IBeaconLocation'])){
                    $deviceCoordinateModel->addNew(
                            $LocationIdentifier['latitude'],
                            $LocationIdentifier['longitude'],
                            $location['d']['id']
                    );
                    $deviceId = $location['d']['id'];
                    unset($location['d']['id']);
                    $location = array_merge($LocationIdentifier,$location['IBeaconLocation']);
                    $brands = $locationModel->findBrandById($location['id']);

                    $categorys = $locationModel->findCategoryById($location['id']);
                    $location['brands'] = array(
                        'list' => array_values($brands)
                    );
                    $location['categorization'] = $categorys;
                    $response['locations'][] = $location;
                    $this->pingAsPresence($location['id'], $deviceId);
                }
            }
        }
        echo json_encode($response);exit;
    }

    /**
     * Ping as Presence
     * 
     * Inserts data into redis
     * @param Integer locationId
     * @param Integer deviceId
     */
    private function pingAsPresence($locationId, $deviceId)
    {
        $email = $this->request->data['user_id'];
        $lat   = (!empty($this->request->data['locations'][0]['latitude'])) ? $this->request->data['locations'][0]['latitude'] : '';
        $long  = (!empty($this->request->data['locations'][0]['longitude'])) ? $this->request->data['locations'][0]['longitude'] : '';
        $rssi  = (!empty($this->request->data['locations'][0]['rssi'])) ? $this->request->data['locations'][0]['rssi'] : '';
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if(empty($email)) return;
        $userModel = new User();
        $user = $userModel->findByEmail($email);
        if(empty($user)) {
            $userModel->set('email', $email);
            $user = $userModel->save();
        }
        try {
            $redis = RedisComponent::getInstance('pingAsPresence');
        } catch (Exception $e) {
            return;
        }
        if(empty($locationId)) return;
        $hKey = $locationId . ':' . $user['User']['id'];
        $hDeviceKey  = 'device:' . $deviceId;
        $data = $redis->hgetall($hKey);
        $date = new DateTime('now', new DateTimeZone('GMT'));
        $currentTime = $date->format('Y-m-d H:i:s');
        $currentTimestamp = $date->getTimestamp();
        $dataSet = array();
        if (!empty($data['timestamp'])) {
            $timeDiff = round(abs(strtotime($currentTime) - strtotime($data['timestamp'])) / 60, 2);
            if ($timeDiff < 20) {
                $dataSet[$hDeviceKey] = (!empty($data[$hDeviceKey])) ? $data[$hDeviceKey] . '$' . $currentTimestamp : $currentTimestamp;
                $dataSet['timestamp'] = $currentTime;
                $dataSet[$hDeviceKey] .= ':' . $lat . ':' . $long . ':' . $rssi;
                $redis->hmset($hKey, $dataSet);
                return;
            } else {
                if (empty($data['count'])) {
                    $dataSet['count'] = 1;
                    $dataSet['timestamp-1'] = $currentTime;
                    $hDeviceKey = $hDeviceKey . '-1';
                    $dataSet[$hDeviceKey] = $currentTimestamp;
                    $dataSet[$hDeviceKey] .= ':' . $lat . ':' . $long . ':' . $rssi;
                    $redis->hmset($hKey, $dataSet);
                    return;
                } else {
                    $timeDiff = round(abs(strtotime($currentTime) - strtotime($data['timestamp-' . $data['count']])) / 60, 2);
                    if ($timeDiff < 20) {
                        $hDeviceKey = $hDeviceKey . '-' . $data['count'];
                        $dataSet[$hDeviceKey] = (!empty($data[$hDeviceKey])) ? $data[$hDeviceKey] . '$' . $currentTimestamp : $currentTimestamp;
                        $dataSet['timestamp-' . $data['count']] = $currentTime;
                        $dataSet[$hDeviceKey] .= ':' . $lat . ':' . $long . ':' . $rssi;
                        $redis->hmset($hKey, $dataSet);
                        return;
                    } else {
                        $dataSet['count'] = $data['count'] + 1;
                        $hDeviceKey = $hDeviceKey . '-' . $dataSet['count'];
                        $dataSet[$hDeviceKey] = $currentTimestamp;
                        $dataSet['timestamp-' . $dataSet['count']] = $currentTime;
                        $dataSet[$hDeviceKey] .= ':' . $lat . ':' . $long . ':' . $rssi;
                        $redis->hmset($hKey, $dataSet);
                        return;
                    }
                }
            }
        }
        $dataSet['timestamp'] = $currentTime;
        $dataSet[$hDeviceKey] = $currentTimestamp;
        $dataSet[$hDeviceKey] .= ':' . $lat . ':' . $long . ':' . $rssi;
        $redis->hmset($hKey, $dataSet);
        return;
    }
}