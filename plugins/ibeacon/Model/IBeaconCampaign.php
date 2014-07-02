<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IBeaconCampaign
 *
 * @author Zotov Maxim <zotov_mv@groupbwt.com>
 */
App::uses('IBeaconModel','ibeacon.Model');

class IBeaconCampaign extends IBeaconModel {

    /**
     *
     * @var string
     */
    public $useTable = 'ibeacon_campaigns';

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
     *
     * @var array
     */
    public $findMethods = array(
        'active' =>  true
    );
    /**
     *
     * @var array
     */
    public $belongsTo = array(
        'Location' => array(
            'className' => 'ibeacon.Location',
            'foreignKey' => 'location_id'
        )
    );
    /**
     *
     * @var array
     */
    public $hasOne = array(
        'CouponConfiguration' => array(
            'className' => 'ibeacon.IBeaconCouponConfiguration',
            'foreignKey' => 'campaign_id'
        )
    );

    /**
     *
     * @var array
     */
    public $hasMany = array(
        'ProximityRules' => array(
            'className' => 'ibeacon.IBeaconCampaignProximityRule',
            'foreignKey' => 'campaign_id'
        ),
        'ScoringRules' => array(
            'className' => 'ibeacon.IBeaconCampaignScoringRule',
            'foreignKey' => 'campaign_id'
        )

    );


    /**
     * List of validation rules. It must be an array with the field name as key and using
     * as value one of the following possibilities
     * @var array
     */
    public $validate = array(
        'location_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'required' => true
            )
        ),
        'name' => array(
            'maxLength' => array(
                'rule' => array('maxLength',256),
                'required' => true
            )
        ),
        'total_coupons' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'required' => true
            )
        ),
        'active' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'required' => true
            )
        ),
        'start_date' => array(
            'date' => array(
                'rule' => array('date'),
                'required' => true
            )
        ),
        'date' => array(
            'datetime' => array(
                'rule' => array('date'),
                'required' => true
            )
        ),
        'minimum_score' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            )
        ),
        'product_identifier' => array(
            'maxLength' => array(
                'rule' => array('maxLength',100),
                'required' => true
            )
        ),
        'ad_partner' => array(
            'maxLength' => array(
                'rule' => array('maxLength',100),
                'required' => true
            )
        )
    );

    /**
     *
     * @param string $state
     * @param array $query
     * @param array $results
     * @return array
     */
    public function _findActive($state, $query, $results = array()) {
        if ($state === 'before') {
            $currentDate = date('Y-m-d H:i:s');
            $query['conditions']['IBeaconCampaign.active'] = 1;
            $query['conditions'][] = array("IBeaconCampaign.total_coupons >= ?" => array(1));
            $query['conditions'][] = array('IBeaconCampaign.start_date <= ?'=> array( $currentDate ));
            $query['conditions'][] = array('IBeaconCampaign.end_date >= ?' => array( $currentDate ));
            return $query;
        }
        return $results;
    }

}
