<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IBeaconDeviceCoordinate
 *
 * @author Zotov Maxim <zotov_mv@groupbwt.com>
 */

App::uses('IBeaconModel','ibeacon.Model');

class IBeaconDeviceCoordinate extends  IBeaconModel{

    /**
     *
     * @var string
     */
    public $useTable = 'ibeacon_device_coordinates';

    /**
     *
     * @var string
     */
    public $id = 'id';


    /**
     *
     * @var string
     */
    public $useDbConfig = 'backstage';

    /**
     * List of validation rules. It must be an array with the field name as key and using
     * as value one of the following possibilities
     * @var array
     */
    public $validate = array(
        'device_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'required' => true
            )
        ),
        'latitude' => array(
            'decimal' => array(
                'rule' => array('decimal'),
                'required' => true
            )
        ),
        'longitude' => array(
            'decimal' => array(
                'rule' => array('decimal'),
                'required' => true
            )
        )
    );
}