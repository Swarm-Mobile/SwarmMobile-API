<?php


/**
 * Description of IBeaconCouponConfiguration
 *
 * @author Zotov Maxim <zotov_mv@groupbwt.com>
 */

App::uses('IBeaconModel','ibeacon.Model');

class IBeaconCouponConfiguration extends IBeaconModel {
  /**
     *
     * @var string
     */
    public $useTable = 'ibeacon_coupon_configuration';

    /**
     *
     * @var string
     */
    public $useDbConfig = 'backstage';

    /**
     *
     * @var string
     */
    public $id = 'id';

     /**
     * List of validation rules. It must be an array with the field name as key and using
     * as value one of the following possibilities
     * @var array
     */
    public $validate = array(
        'campaign_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'required' => true,
            )
        ),
        'image_url' => array(
            'maxLength' => array(
                'rule' => array('maxLength',512)
            )
        ),
        'external_url' => array(
            'maxLength' => array(
                'rule' => array('maxLength',512)
            )
        ),
        'title' =>  array(
            'maxLength' => array(
                'rule' => array('maxLength',256),
                'required' => true,
            )
        ),
        'text' => array(
            'maxLength' => array(
                'rule' => array('maxLength',256),
                'required' => true,
            )
        ),
        'delivery_text' => array(
            'maxLength' => array(
                'rule' => array('maxLength',256)
            )
        )
    );
}