<?php

App::uses('AppModel', 'Model');

class AccountManager extends AppModel
{

    public $useDbConfig  = 'backstage';
    public $useTable     = 'accountmanager';
    public $displayField = 'lastname';
    public $id           = 'id';

    public function __construct ($id = false, $table = null, $ds = null)
    {
        $this->setValidationFields(['firstname', 'lastname']);
        parent::__construct($id, $table, $ds);
    }

}
