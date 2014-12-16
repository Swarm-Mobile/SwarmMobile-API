<?php

App::uses('AppModel', 'Model');

class Employee extends AppModel
{

    public $useDbConfig  = 'backstage';
    public $useTable     = 'employee';
    public $displayField = 'lastname';
    public $id           = 'id';

    public function __construct ($id = false, $table = null, $ds = null)
    {
        $this->setValidationFields(['firstname', 'lastname']);
        parent::__construct($id, $table, $ds);
    }

}
