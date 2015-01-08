<?php

App::uses('Model', 'Model');
App::uses('Device', 'Model/Device');
App::uses('DeviceEnvironment', 'Model/Device');
App::uses('Location', 'Model/Location');
App::uses('LocationSetting', 'Model/Location');

class SDKController extends AppController
{

    public function whatIsHere ()
    {
        $devices = $this->request->data('devices');
        $result  = ['data' => [], 'error' => []];
        if (is_array($devices)) {
            $deviceModel       = new Device();
            $deviceEnvironment = new DeviceEnvironment();
            $location          = new Location();
            $locationSetting   = new LocationSetting();
            foreach ($devices as $device) {
                $location->clear();
                $found = false;
                $env   = $deviceEnvironment->find('first', ['conditions' => ['uuid' => $device['uuid']]]);                
                if (!empty($env)) {
                    $dbDevice = $deviceModel->find('first', ['conditions' => [
                            'minor'           => $device['minor'],
                            'major'           => $device['major'],
                            'devicestatus_id' => DeviceStatus::$DEPLOYED,
                            'location_id >'   => 0
                    ]]);
                    if (!empty($dbDevice)) {                        
                        $found              = true;
                        $locationSetting->clear();
                        $location->read(null, $dbDevice['Device']['location_id']);
                        if(!empty($location->data['Location'])){
                            $locationSetting->setLocationId($location->id);
                            $device['location'] = [
                                'id'             => $location->id,
                                'name'           => $location->data['Location']['name'],
                                'address1'       => $locationSetting->getSettingValue(LocationSetting::ADDRESS1),
                                'address2'       => $locationSetting->getSettingValue(LocationSetting::ADDRESS2),
                                'city'           => $locationSetting->getSettingValue(LocationSetting::CITY),
                                'state'          => $locationSetting->getSettingValue(LocationSetting::STATE),
                                'country'        => $locationSetting->getSettingValue(LocationSetting::COUNTRY),
                                'zipcode'        => $locationSetting->getSettingValue(LocationSetting::ZIPCODE),
                                'brands'         => array_filter([
                                    $locationSetting->getSettingValue(LocationSetting::BRAND_1),
                                    $locationSetting->getSettingValue(LocationSetting::BRAND_2),
                                    $locationSetting->getSettingValue(LocationSetting::BRAND_3),
                                    $locationSetting->getSettingValue(LocationSetting::BRAND_4),
                                    $locationSetting->getSettingValue(LocationSetting::BRAND_5),
                                    $locationSetting->getSettingValue(LocationSetting::BRAND_6),
                                    $locationSetting->getSettingValue(LocationSetting::BRAND_7),
                                    $locationSetting->getSettingValue(LocationSetting::BRAND_8),
                                    $locationSetting->getSettingValue(LocationSetting::BRAND_9),
                                    $locationSetting->getSettingValue(LocationSetting::BRAND_10),
                                ]),
                                'categorization' => [
                                    'naics_code' => $locationSetting->getSettingValue(LocationSetting::NAICS_CODE),
                                    'iab_tier1'  => $locationSetting->getSettingValue(LocationSetting::IAB_TIER_1_CATEGORY),
                                    'iab_tier2'  => $locationSetting->getSettingValue(LocationSetting::IAB_TIER_2_CATEGORY),
                                ]
                            ];
                        } else {                            
                            $device['error'] = 'Location not found';
                        }
                    }
                    else {
                        $device['error'] = 'Device not found';
                    }
                }
                else {
                    $device['error'] = 'UUID not found';
                }
                $result[($found) ? 'data' : 'error'][] = $device;
            }
        }
        return new JsonResponse(['body' => $result]);
    }
    
}
