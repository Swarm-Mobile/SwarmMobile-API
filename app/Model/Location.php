<?php

App::uses('AppModel', 'Model');

class Location extends AppModel {

    public $useDbConfig = 'backstage';
    public $useTable = 'location';
    public $displayField = 'name';
    public $id = 'id';
    public $hasAndBelongsToMany = array(
        "Setting" => array(
            'joinTable' => 'location_setting'
        )
    );
    public $validate = array(
        'name' => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
                'required' => true,
            ),
        ),
        'address1' => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
                'required' => true,
            ),
        ),
        'city' => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
                'required' => true,
            ),
        ),
        'state' => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
                'required' => true,
            ),
        ),
        'zipcode' => array(
            'numeric' => array(
                'rule' => array('notEmpty'),
                'required' => true,
            ),
        ),
        'email' => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
                'required' => true,
            ),
        ),
        'accountmanager_id' => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
                'required' => false,
            ),
        ),
    );

}
