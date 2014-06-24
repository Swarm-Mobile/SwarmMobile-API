<?php

App::uses('AppModel', 'Model');

class SettingGroup extends AppModel {

    public $useDbConfig = 'backstage';
    public $useTable = 'settinggroup';
    public $displayField = 'name';
    public $id = 'id';
    public $hasMany = array("Setting");

}
