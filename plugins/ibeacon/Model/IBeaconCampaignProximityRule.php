<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IBeaconCampaignProximityRule
 *
 * @author Zotov Maxim <zotov_mv@groupbwt.com>
 */
class IBeaconCampaignProximityRule extends IBeaconModel {
     /**
     *
     * @var satring
     */
    public $useTable = 'ibeacon_campaign_proximity_rules';

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
    public $validate = array(
        'campaign_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'required' => true,
            )
        ),
        'type' => array(
            'inList' => array(
                'rule'    => array('inList', array(
                    'outbound_lost',
                    'outbound_unknown',
                    'outbound_far',
                    'outbound_near',
                    'inbound_immediate',
                    'inbound_unknown',
                    'inbound_far',
                    'inbound_near'
                )),
                'required' => true
            )
        )
    );
}