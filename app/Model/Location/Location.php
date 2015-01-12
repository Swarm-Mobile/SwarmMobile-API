<?php

App::uses('AppModel', 'Model');
App::uses('DeviceType', 'Model/Device');
App::uses('LocationSetting', 'Model/Location');

class Location extends AppModel
{

    public $useDbConfig  = 'backstage';
    public $useTable     = 'location';
    public $displayField = 'name';
    public $id           = 'id';

    public function __construct ($id = false, $table = null, $ds = null)
    {
        $this->setValidationFields(['name']);
        parent::__construct($id, $table, $ds);
    }

    public function nameAddressCombination ($data, $isOverridingName = false, $locationId = 0)
    {
        $count = 0;
        if (!$isOverridingName) {
            $location = $this->find('first', ['conditions' => ['id' => $locationId]]);
            if (empty($location) || empty($locationId)) {
                throw new Swarm\UnprocessableEntityException(SwarmErrorCodes::DUPLICATE_NAME_ADDRESS_COMBINATION);
            }
            $name = $location['Location']['name'];
        }
        else {
            $name = $data['name'];
        }
        $locations = $this->find('all', ['conditions' => ['name' => $name]]);
        if (!empty($locations)) {
            foreach ($locations as $location) {
                $cLocationId     = $location['Location']['id'];
                $locationSetting = new LocationSetting();
                $locationSetting->setLocationId($cLocationId);
                if (
                    $data['address1'] == $locationSetting->getSettingValue(LocationSetting::ADDRESS1) && 
                    $data['city'] == $locationSetting->getSettingValue(LocationSetting::CITY) && 
                    $location['Location']['name'] == $name && 
                    $locationId != $cLocationId
                ) {
                    $count++;
                }
            }
        }
        return $count;
    }

    public function countryCodeExists ($code)
    {
        $model   = new Model(null, 'country', 'backstage');
        $country = $model->find('first', ['conditions' => ['code' => $code]]);
        return !empty($country);
    }

    public function getDevices ()
    {
        if (empty($this->id)) {
            throw new Swarm\ApplicationErrorException(SwarmErrorCodes::LOCATION_NOT_INITIALIZED);
        }
        $model    = new Model(null, 'device', 'backstage');
        $query    = [
            'fields' => [
                'Device.id',
                'Device.serial',
                'Device.alias',
                'DeviceType.name',
            ],
            'table'  => 'device',
            'alias'  => 'Device',
            'joins'  => [
                [
                    'table'      => 'devicetype',
                    'alias'      => 'DeviceType',
                    'type'       => 'INNER',
                    'conditions' => [
                        'Device.devicetype_id= DeviceType.id',
                        'Device.devicetype_id' => DeviceType::$PORTAL,
                        'Device.location_id'   => $this->id,
                    ]
                ]
            ]
        ];
        $db       = $model->getDataSource();
        $querySQL = $db->buildStatement($query, $model);
        $devices  = $db->fetchAll($querySQL);
        $return   = [];
        if (!empty($devices)) {
            foreach ($devices as $device) {
                $return[] = [
                    'id'            => $device['Device']['id'],
                    'serial_number' => $device['Device']['serial'],
                    'alias'         => $device['Device']['alias'],
                    'type'          => $device['DeviceType']['name'],
                ];
            }
        }
        return $return;
    }

}
