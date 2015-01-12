<?php

App::uses('DeviceStatus', 'Model/Device');
App::uses('AppModel', 'Model');

class Device extends AppModel
{

    public $useDbConfig  = 'backstage';
    public $useTable     = 'device';
    public $displayField = 'name';
    public $id           = 'id';

    public function __construct ($id = false, $table = null, $ds = null)
    {
        $this->setValidationFields([
            'devicetype_id',
            'major',
            'minor',
            'deviceenvironment_id',
            'serial',
            'manufacturer_serial',
            'mac',
            'alias',
        ]);
        parent::__construct($id, $table, $ds);
    }

    public function beforeSave ($options = [])
    {
        $id = ($this->id > 0) ? $this->id : ((isset($this->data['Device']) && isset($this->data['Device']['id'])) ? $this->data['Device']['id'] : 0);
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
