<?php

App::uses('AppModel', 'Model');

class Reseller extends AppModel
{

    public $useDbConfig  = 'backstage';
    public $useTable     = 'reseller';
    public $displayField = 'lastname';
    public $id           = 'id';

    public function __construct ($id = false, $table = null, $ds = null)
    {
        $this->setValidationFields([
            'firstname',
            'lastname',
            'address1',
            'zipcode',
            'city',
            'state',
            'company'
        ]);
        parent::__construct($id, $table, $ds);
    }

}
