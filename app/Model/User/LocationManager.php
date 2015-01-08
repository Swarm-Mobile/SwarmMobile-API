<?php

App::uses('AppModel', 'Model');

class LocationManager extends AppModel
{

    public $useDbConfig  = 'backstage';
    public $useTable     = 'locationmanager';
    public $displayField = 'firstname';
    public $id           = 'id';

    public function __construct ($id = false, $table = null, $ds = null)
    {
        $this->setValidationFields(['firstname', 'lastname']);
        parent::__construct($id, $table, $ds);
    }

}
