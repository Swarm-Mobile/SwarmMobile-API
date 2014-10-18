<?php

App::uses('AppController', 'Controller');

class DeviceController extends AppController
{

    public function assign ()
    {
        $locationId   = $this->request->data['location_id'];
        $userId       = $this->request->data['user_id'];
        $serialNumber = $this->request->data['user_id'];
        $deviceType   = $this->request->data['device_type'];
        $ts           = $this->request->data['ts'];

        return [
            'device_assigned' => true OR false,
            'message'         => 'message'
        ];
    }

    public function checkForUpdates ()
    {
        $deviceType      = $this->request->query['device_type'];
        $serialNumber    = $this->request->query['serial_number'];
        $userId          = $this->request->query['user_id'];
        $firmwareVersion = $this->request->firmware_version[''];
    }

    public function getStatus ()
    {
        [
            'type',
            'serial',
            'location_id',
            'user_id',
            'battery_level',
            'lat',
            'long',
            'time',
            'store_open',
            'store_close',
            'firmware_version',
            'app_version'
        ];

        $deviceType;
        $deviceSerial;
        $locationId;
        $userId;
        $batteryLevel;
        $lat;
        $long;
        $deviceTime;
        $storeOpen;
        $storeClose;
        $firmwareVersion;
        $appVersion;
    }

    public function setStatus ()
    {
        $p = $this->request->data;
        if (empty($p['type']) || !in_array(strtolower($p['type']), ['ping', 'portal', 'presence'])) {
            throw new Exception('type is required and must be presence, portal or presence.');
        }
        if(empty($p['serial_number'])){
            throw new Exception('serial_number is required.');            
        }
        if(empty($p['location_id']) || !ValidatorComponent::isPositiveInt($p['location_id'])){
            throw new Exception('location_id is required and must be an integer.');                        
        }
        if(empty($p['user_id']) || !ValidatorComponent::isPositiveInt($p['user_id'])){        
            throw new Exception('user_id is required and must be an integer.');                                    
        }
               
        $paramSet    = [
            'type', 'serial_number', 'location_id', 'user_id',
            'battery_level', 'lat', 'long', 'time', 'store_open',
            'store_close', 'firmware_version', 'app_version'
        ];
        
        $toSet = [];
        foreach($paramSet as $param){
            if(!empty($p[$param])){
                $toSet[$param] = $p[$param];
            }
        }
        
        $this->User->recursive = -1;
        $this->User->read(null, $p['user_id']);
        
    }

}
