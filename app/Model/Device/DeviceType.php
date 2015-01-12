<?php

App::uses('AppModel', 'Model');

class DeviceType extends AppModel
{

    public $useDbConfig         = 'backstage';
    public $useTable            = 'devicetype';
    public $displayField        = 'name';
    public $id                  = 'id';
    public static $PRESENCE     = 1;
    public static $PORTAL       = 2;
    public static $PING         = 3;
    public static $ID_FROM_NAME = [
        'presence' => 1,
        'portal'   => 2,
        'ping'     => 3,
    ];
    public static $NAME_FROM_ID = [
        1 => 'presence',
        2 => 'portal',
        3 => 'ping',
    ];

    public function __construct ($id = false, $table = null, $ds = null)
    {
        $this->setValidationFields(['name', 'description', 'source']);
        parent::__construct($id, $table, $ds);
    }

}
