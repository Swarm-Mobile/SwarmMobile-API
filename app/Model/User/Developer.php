<?php

App::uses('AppModel', 'Model');

class Developer extends AppModel
{

    public $useDbConfig  = 'backstage';
    public $useTable     = 'developer';
    public $displayField = 'lastname';
    public $id           = 'id';

    public function __construct ($id = false, $table = null, $ds = null)
    {
        $this->setValidationFields(['firstname', 'lastname', 'company']);
        parent::__construct($id, $table, $ds);
    }

}
