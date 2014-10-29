<?php

App::uses('AppModel', 'Model');

class DeviceEnvironment extends AppModel {

    public $useDbConfig = 'backstage';
    public $useTable = 'deviceenvironment';
    public $displayField = 'name';
    public $id = 'id';
    public $hasMany = array("Device");
    public $validate = array(
        'name' => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
                'required' => true,
            ),
        ),
        'uuid' => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
                'required' => true,
            ),
        ),
    );

}
