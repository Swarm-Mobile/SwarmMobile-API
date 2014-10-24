<?php

App::uses('AppModel', 'Model');

class DeviceStatus extends AppModel
{

    public $useDbConfig  = 'backstage';
    public $useTable     = 'devicestatus';
    public $displayField = 'name';
    public $id           = 'id';
    public $hasMany      = ["Device"];
    public static $INVENTORY = 1;
    public static $RESELLER  = 2;
    public static $DEPLOYED  = 3;
    public static $LOST      = 4;
    public static $RETURNED  = 5;
    public static $DEFECTIVE = 6;

}
