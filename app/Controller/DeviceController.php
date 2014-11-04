<?php

App::uses('AppController', 'Controller');
App::uses('Location', 'Model');
App::uses('User', 'Model');
App::uses('Device', 'Model');
App::uses('DeviceType', 'Model');
require_once(APP . 'Controller/Component/S3FactoryComponent.php');

class DeviceController extends AppController
{

    public $uses = ['Location', 'User', 'Device', 'DeviceType'];

    /**
     * Assigns a Device to a Location. If the Device doesn't exists 
     * or is already assigned, throws an Exception.
     * 
     * @throws Exception
     */
    public function assign ()
    {
        list($locationId, $userId, $deviceType, $serialNumber, $ts) = $this->validateParams($this->request->data, [
            'location_id'   => ['required', 'positive_int'],
            'user_id'       => ['required', 'positive_int'],
            'type'          => ['required', 'device_type'],
            'serial_number' => ['required'],
            'ts'            => ['required'],
        ]);
        $message         = 'Device updated successfully';
        $device_assigned = true;
        $device          = $this->Device->find('first', ['conditions' => ['Device.serial' => $serialNumber]]);
        try {
            if (empty($device)) {
                throw new Exception('Invalid device');
            }
            //elseif (!empty($device['Device']['location_id'])) {
            //    throw new Exception('This device is already assigned to a location', 401);
            //}
            elseif ($device['Device']['devicetype_id'] != DeviceType::$ID_FROM_NAME[strtolower($deviceType)]) {
                throw new Exception("This device doesn't correspond with this device type", 401);
            }
            else {
                $this->Device->read(null, $device['Device']['id']);
                $this->Device->save(['Device' => ['location_id' => $locationId]], true, ['location_id']);
            }
        }
        catch (Exception $e) {
            $device_assigned = false;
            $message         = $e->getMessage();
        }

        $result = [
            'device_assigned' => $device_assigned,
            'message'         => $message
        ];
        return new JsonResponse(['body' => $result]);
    }

    /**
     * Returns if for a particular Device Type and version, there
     * are firmware updates. In case of the device sent by param
     * doesn't match with the device type sent by param, throws
     * an Exception.
     * 
     * @throws Exception
     */
    public function checkForUpdates ()
    {
        list($deviceType, $serialNumber, $userId, $firmwareVersion) = $this->validateParams($this->request->query, [
            'type'             => ['required', 'device_type'],
            'serial_number'    => ['required'],
            'user_id'          => ['required', 'positive_int'],
            'firmware_version' => ['required']
        ]);
        $device = $this->Device->find('first', ['conditions' => ['Device.serial' => $serialNumber]]);
        if (empty($device)) {
            throw new Exception('Invalid device');
        }
        elseif ($device['Device']['devicetype_id'] != DeviceType::$ID_FROM_NAME[strtolower($deviceType)]) {
            throw new Exception("This device doesn't correspond with this device type", 401);
        }
        else {
            switch (strtolower($deviceType)) {
                case 'portal':
                    $s3factory = new S3FactoryComponent(new ComponentCollection());
                    $s3Client  = $s3factory->loadS3();
                    $sourceUrl = $s3Client->getObjectUrl(
                            'swarm-device-firmware', 'Swarm_v1.11d_NoSerial.hex', '20 minutes'
                    );

                    $result = [
                        "update_available" => $firmwareVersion != '1.11',
                        "firmware_version" => "1.11",
                        "source"           => $sourceUrl
                    ];
                    break;
                case 'ping':
                    $result = [
                        "update_available" => false,
                        "firmware_version" => null,
                        "source"           => null
                    ];
                    break;
                case 'presence':
                    $result = [
                        "update_available" => false,
                        "firmware_version" => null,
                        "source"           => null
                    ];
                    break;
            }
        }
        return new JsonResponse(['body' => $result]);
    }

    /**
     * Get the device Status (battery_level, lat, long...) of 
     * a particular device.
     * 
     * @throws Exception
     */
    public function getStatus ()
    {
        list($locationId, $userId, $deviceType, $serialNumber) = $this->validateParams($this->request->query, [
            'location_id'   => ['required', 'positive_int'],
            'user_id'       => ['required', 'positive_int'],
            'type'          => ['required', 'device_type'],
            'serial_number' => ['required'],
        ]);
        $device = $this->Device->find('first', ['conditions' => ['Device.serial' => $serialNumber]]);
        if (empty($device)) {
            throw new Exception('Invalid device');
        }
        elseif ($device['Device']['location_id'] != $locationId) {
            throw new Exception('This device is not assigned to this location.', 401);
        }
        elseif ($device['Device']['devicetype_id'] != DeviceType::$ID_FROM_NAME[strtolower($deviceType)]) {
            throw new Exception("This device doesn't correspond with this device type", 401);
        }
        else {
            $result = [
                'type'             => $deviceType,
                'device_serial'    => $serialNumber,
                'location_id'      => $locationId,
                'battery_level'    => $device['Device']['battery_level'],
                'lat'              => $device['Device']['lat'],
                'long'             => $device['Device']['long'],
                'last_sync'        => $device['Device']['last_sync'],
                'store_open'       => $device['Device']['store_open'],
                'store_close'      => $device['Device']['store_close'],
                'firmware_version' => $device['Device']['firmware_version'],
                'app_version'      => $device['Device']['app_version']
            ];
            return new JsonResponse(['body' => $result]);
        }
    }

    /**
     * Sets the device Status (battery_level, lat, long...) of 
     * a particular device.
     * 
     * @throws Exception
     */
    public function setStatus ()
    {
        $opt    = ['last_sync' => date('Y-m-d H:i:s')];
        list(
                $deviceType,
                $serialNumber,
                $locationId,
                $userId,
                $opt['battery_level'],
                $opt['lat'],
                $opt['long'],
                $opt['time'],
                $opt['store_open'],
                $opt['store_close'],
                $opt['firmware_version'],
                $opt['app_version']
                ) = $this->validateParams($this->request->data, [
            'type'             => ['required', 'device_type'],
            'serial_number'    => ['required'],
            'location_id'      => ['required', 'positive_int'],
            'user_id'          => ['required', 'positive_int'],
            'battery_level'    => [],
            'lat'              => [],
            'long'             => [],
            'time'             => [],
            'store_open'       => [],
            'store_close'      => [],
            'firmware_version' => [],
            'app_version'      => []
        ]);
        $opt    = array_filter($opt);
        $device = $this->Device->find('first', ['conditions' => ['Device.serial' => $serialNumber]]);
        if (empty($device)) {
            throw new Exception('Invalid device');
        }
        elseif ($device['Device']['location_id'] != $locationId) {
            throw new Exception('This device is not assigned to this location.', 401);
        }
        elseif ($device['Device']['devicetype_id'] != DeviceType::$ID_FROM_NAME[strtolower($deviceType)]) {
            throw new Exception("This device doesn't correspond with this device type", 401);
        }
        else {
            $this->Device->read(null, $device['Device']['id']);
            $this->Device->save(['Device' => $opt], true, array_keys($opt));
            $device = $this->Device->find('first', ['conditions' => ['Device.serial' => $serialNumber]]);
            $result = [
                'type'             => $deviceType,
                'device_serial'    => $serialNumber,
                'location_id'      => $locationId,
                'battery_level'    => $device['Device']['battery_level'],
                'lat'              => $device['Device']['lat'],
                'long'             => $device['Device']['long'],
                'last_sync'        => $device['Device']['last_sync'],
                'store_open'       => $device['Device']['store_open'],
                'store_close'      => $device['Device']['store_close'],
                'firmware_version' => $device['Device']['firmware_version'],
                'app_version'      => $device['Device']['app_version']
            ];
            return new JsonResponse(['body' => $result]);
        }
    }

}
