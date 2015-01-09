<?php

App::uses('AppController', 'Controller');
App::uses('DeviceType', 'Model/Device');

require_once(APP . 'Controller/Component/S3FactoryComponent.php');

class DeviceController extends AppController
{

    protected $location;
    protected $user;
    protected $device;
    protected $deviceType;

    public function getLocation ()
    {
        if (empty($this->location)) {
            App::uses('Location', 'Model/Location');
            $this->location = new Location();
        }
        return $this->location;
    }

    public function getUser ()
    {
        if (empty($this->user)) {
            App::uses('User', 'Model/User');
            $this->user = new User();
        }
        return $this->user;
    }

    public function getDevice ()
    {
        if (empty($this->device)) {
            App::uses('Device', 'Model/Device');
            $this->device = new Device();
        }
        return $this->device;
    }

    public function setLocation (Location $location)
    {
        $this->location = $location;
        return $this;
    }

    public function setUser (User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function setDevice (Device $device)
    {
        $this->device = $device;
        return $this;
    }

    /**
     * Assigns a Device to a Location. If the Device doesn't exists 
     * or is already assigned, throws an Exception.
     * 
     * @throws Exception
     */
    public function assign ()
    {
        $this->request->data['serial'] = $this->request->data('serial_number');
        $errors                        = AppModel::validationErrors(['location_id', 'user_id', 'type', 'serial', 'ts'], $this->request->data, false);
        if (!empty($errors)) {
            throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($errors));
        }

        $locationId   = $this->request->data['location_id'];
        $deviceType   = $this->request->data['type'];
        $serialNumber = $this->request->data['serial'];

        $deviceModel = $this->getDevice();
        $device      = $deviceModel->find('first', ['conditions' => ['Device.serial' => $serialNumber]]);
        try {
            $this->_checkDevice($device, $deviceType, true);
            $device         = $deviceModel->find('first', ['conditions' => ['Device.serial' => $serialNumber]]);
            $deviceModel->read(null, $device['Device']['id']);
            $deviceModel->save(['Device' => ['location_id' => $locationId]], true, ['location_id']);
            $deviceAssigned = true;
            $message        = 'Device updated successfully';
        }
        catch (Exception $e) {
            $deviceAssigned = false;
            $message        = $e->getMessage();
        }

        return new JsonResponse(['body' => [
                'device_assigned' => $deviceAssigned,
                'message'         => $message
        ]]);
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
        $this->request->query['serial'] = $this->request->query('serial_number');
        $errors                         = AppModel::validationErrors(['type', 'serial', 'user_id', 'firmware_version'], $this->request->query, false);
        if (!empty($errors)) {
            throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($errors));
        }

        $deviceType      = $this->request->query('type');
        $serialNumber    = $this->request->query('serial');
        $firmwareVersion = $this->request->query('firmware_version');

        $deviceModel = $this->getDevice();
        $device      = $deviceModel->find('first', ['conditions' => ['Device.serial' => $serialNumber]]);
        $this->_checkDevice($device, $deviceType, false);

        switch (strtolower($deviceType)) {
            case 'portal':
                if (!in_array($firmwareVersion, ['1.05', '1.07', '1.11'])) {
                    throw new Swarm\UnprocessableEntityException(SwarmErrorCodes::DEVICE_CHECKFORUPDATES_PORTAL_FIRMWARE_INVALID);
                }
                $s3factory = new S3FactoryComponent(new ComponentCollection());
                $s3Client  = $s3factory->loadS3();
                $sourceUrl = $s3Client->getObjectUrl('swarm-device-firmware', 'Swarm_v1.11d_ImgB.bin', '20 minutes');

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
        $this->request->query['serial'] = $this->request->query('serial_number');        
        $errors                         = AppModel::validationErrors(['location_id', 'user_id', 'type', 'serial'], $this->request->query, false);
        if (!empty($errors)) {
            throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($errors));
        }
        $locationId   = $this->request->query['location_id'];
        $deviceType   = $this->request->query['type'];
        $serialNumber = $this->request->query['serial'];

        $deviceModel = $this->getDevice();
        $device      = $deviceModel->find('first', ['conditions' => ['Device.serial' => $serialNumber]]);
        $this->_checkDevice($device, $deviceType, false, $locationId);

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

    /**
     * Sets the device Status (battery_level, lat, long...) of 
     * a particular device.
     * 
     * @throws Exception
     */
    public function setStatus ()
    {
        $this->request->data['serial'] = $this->request->data('serial_number');
        $errors                        = AppModel::validationErrors(['location_id', 'user_id', 'type', 'serial'], $this->request->data, false);
        if (!empty($errors)) {
            throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($errors));
        }
        $locationId   = $this->request->data['location_id'];
        $deviceType   = $this->request->data['type'];
        $serialNumber = $this->request->data['serial'];

        $opt = ['last_sync' => date('Y-m-d H:i:s')];
        foreach (['battery_level', 'lat', 'long', 'time', 'store_open', 'store_close', 'firmware_version', 'app_version'] as $var) {
            $opt[$var] = $this->request->data($var);
        }
        $opt                  = array_filter($opt);
        $deviceModel          = $this->getDevice();
        $device               = $deviceModel->find('first', ['conditions' => ['Device.serial' => $serialNumber]]);
        $this->_checkDevice($device, $deviceType, false, $locationId);      
        $deviceModel->read(null, $device['Device']['id']);
        $deviceModel->save($opt);
        $this->request->query = $this->request->data;        
        return $this->getStatus();
    }

    private function _checkDevice ($device, $deviceType, $createDevice = false, $locationId = null)
    {
        if (empty($device)) {
            if ($createDevice) {
                $this->_createDevice();
            }
            else {
                throw new Swarm\UnprocessableEntityException(SwarmErrorCodes::DEVICE_STATUS_DEVICE_NOTFOUND);
            }
        }
        elseif ($locationId != null && $device['Device']['location_id'] != $locationId) {
            throw new Swarm\UnprocessableEntityException(SwarmErrorCodes::DEVICE_STATUS_DEVICE_LOCATION_MISMATCH);
        }
        elseif ($device['Device']['devicetype_id'] != DeviceType::$ID_FROM_NAME[strtolower($deviceType)]) {
            throw new Swarm\UnprocessableEntityException(SwarmErrorCodes::DEVICE_STATUS_DEVICE_TYPE_MISMATCH);
        }
        return true;
    }

    private function _createDevice ()
    {
        $data                                   = [];
        $data['Device']['serial']               = $this->request->data['serial'];
        $data['Device']['location_id']          = $this->request->data['location_id'];
        $deviceTypeId                           = DeviceType::$ID_FROM_NAME[strtolower($this->request->data['type'])];
        $data['Device']['devicetype_id']        = !empty($deviceTypeId) ? $deviceTypeId : DeviceType::$PORTAL;
        $data['Device']['ts_creation']          = date('Y-m-d H:i:s');
        $data['Device']['deviceenvironment_id'] = 2;
        $data['Device']['alias']                = "LocationId-" . $this->request->data['location_id'];
        $device                                 = $this->getDevice();
        $device->create($data, false);
        if ($device->save(null, false)) {
            $key = SwarmErrorCodes::DEVICE_ASSIGN_DEVICE_CREATE;
            NewRelicComponent::addCustomParameter("MISSING_DEVICE_CREATED_VIA_API", 1);
            NewRelicComponent::addCustomParameter("deviceId", $device->id);
            NewRelicComponent::addCustomParameter("deviceAlias", $data['Device']['alias']);            
        }
        else {
            $key = SwarmErrorCodes::DEVICE_ASSIGN_DEVICE_CREATE_ERROR;
            throw new Swarm\UnprocessableEntityException(SwarmErrorCodes::DEVICE_ASSIGN_DEVICE_CREATE_ERROR);
        }
    }

}
