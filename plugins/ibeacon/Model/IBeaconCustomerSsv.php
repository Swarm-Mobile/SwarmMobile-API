<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IBeaconCustomerSsv
 *
 * @author Zotov Maxim <zotov_mv@groupbwt.com>
 */
App::uses('IBeaconModel','ibeacon.Model');

class IBeaconCustomerSsv extends IBeaconModel {



    /**
     *
     * @var string
     */
    public $useTable = 'ibeacon_customer_ssv';

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
        'customer_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'required' => true,
            )
        ),
        'name' => array(
            'maxLength' => array(
                'rule' => array('maxLength',128),
                'required' => true,
            )
        ),
        'value' => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
                'required' => true,
            )
        )
    );
}