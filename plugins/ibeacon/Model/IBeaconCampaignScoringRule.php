<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IBeaconCampaignScoringRule
 *
 * @author Zotov Maxim <zotov_mv@groupbwt.com>
 */
class IBeaconCampaignScoringRule extends IBeaconModel {

    /**
     *
     * @var string
     */
    public $useTable = 'ibeacon_campaign_scoring _rules';


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
        'name' => array(
            'maxLength' => array(
                'rule' => array('maxLength',128),
                'required' => true,
            )
        ),
        'rule' => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
                'required' => true,
            )
        )
    );

    /**
     *
     * @param int $campaignId
     * @param array $fields
     * @return array
     */
    public function getListByCampaignId ($campaignId , $fields = array()) {
        return $this->find('list',array(
            'conditions' => array(
                'campaign_id' => $campaignId
            ),
            'fields' => $fields
        ));

    }
}