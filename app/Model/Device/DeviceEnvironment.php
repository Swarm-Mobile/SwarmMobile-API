<?php

App::uses('AppModel', 'Model');

class DeviceEnvironment extends AppModel
{

    public $useDbConfig  = 'backstage';
    public $useTable     = 'deviceenvironment';
    public $displayField = 'name';
    public $id           = 'id';

    public function __construct ($id = false, $table = null, $ds = null)
    {
        $this->setValidationFields(['name', 'uuid']);
        parent::__construct($id, $table, $ds);
    }

}
