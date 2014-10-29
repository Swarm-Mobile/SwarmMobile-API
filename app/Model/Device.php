<?php

App::uses('AppModel', 'Model');

class Device extends AppModel
{

    public $useDbConfig  = 'backstage';
    public $useTable     = 'device';
    public $displayField = 'name';
    public $id           = 'id';
    public $belongsTo    = array (
        "Location",
        "DeviceType"        => array (
            'className'  => 'DeviceType',
            'foreignKey' => 'devicetype_id'
        ),
        "DeviceStatus"      => array (
            'className'  => 'DeviceStatus',
            'foreignKey' => 'devicestatus_id'
        ),
        "DeviceEnvironment" => array (
            'className'  => 'DeviceEnvironment',
            'foreignKey' => 'deviceenvironment_id'
        )
    );

    public function beforeSave ($options = array ())
    {
        $id = ($this->id > 0) ? $this->id : $this->data['Device']['id'];
        if (!empty($id)) {
            $oDevice = new Device();
            $oDevice = $oDevice->find('first', ['conditions' => ['Device.id' => $id]]);
            if (!empty($this->data['Device']['devicestatus_id'])) {
                if ($oDevice['Device']['devicestatus_id'] == $this->data['Device']['devicestatus_id']) {
                    if ($this->data['Device']['location_id'] > 0) {
                        $this->data['Device']['devicestatus_id'] = DeviceStatus::$DEPLOYED;
                    }
                    else if ($this->data['Device']['reseller_id'] > 0) {
                        $this->data['Device']['devicestatus_id'] = DeviceStatus::$RESELLER;
                    }
                    else {
                        $this->data['Device']['devicestatus_id'] = DeviceStatus::$INVENTORY;
                    }
                }
            }
        }
        return true;
    }

}
