<?php

App::uses('AppModel', 'Model');

class UserType extends AppModel {

    public $useDbConfig = 'backstage';
    public $useTable = 'usertype';
    public $displayField = 'name';
    public $id = 'id';
    public $hasMany = array("User");
    public static $GUEST = 0;
    public static $SUPER_ADMIN = 1;
    public static $ACCOUNT_MANAGER = 2;
    public static $RESELLER = 3;
    public static $LOCATION_MANAGER = 4;
    public static $EMPLOYEE = 5;
    public static $DEVELOPER = 6;

}
