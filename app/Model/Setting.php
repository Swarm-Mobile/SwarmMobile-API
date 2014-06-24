<?php

App::uses('AppModel', 'Model');

class Setting extends AppModel {

    public $useDbConfig = 'backstage';
    public $useTable = 'setting';
    public $displayField = 'name';
    public $id = 'id';
    public $belongsTo = array("SettingGroup" => array(
            'className' => 'SettingGroup',
            'foreignKey' => 'settinggroup_id'
    ));

}
