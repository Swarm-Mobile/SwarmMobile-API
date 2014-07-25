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
App::uses('IBeaconModel','ibeacon.Model');

class IBeaconCampaignProximityRule extends IBeaconModel {

    public $types = array(
        /**
         * Immediate
         */
        'I' => array(
            'name'      => 'immediate',
            'distance'  => 1
        ),
        /**
         * Near the beacon.
         */
        'N' => array(
            'name'      => 'near',
            'distance'  => 2
        ),
        /**
         * Far from the beacon.
         */
        'F' => array(
            'name'      => 'far',
            'distance'  => 3
        ),
        /**
         * Unknown proximity.
         */
        'U' => array(
            'name'      => 'unknown',
            'distance'  => 4
        ),
        /**
         * No proximity, the signal is lost.
         */
        'L' => array(
            'name'      => 'lost',
            'distance'  => 5
        )
    );
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

    /**
     *
     * @param int $campaignId
     * @param string $previous
     * @param string $current
     * @return boolean
     */
    public function fits ($campaignId,$previous,$current) {
        if(empty($previous) || empty($current)){
            return false;
        }
        if(!isset($this->types[$previous]) || !isset($this->types[$current])){
            return false;
        }
        $rules =  $this->findByCampaignId($campaignId);
        if(empty($rules)){
            return false;
        }
        // Inbound
        if($this->types[$previous]['distance'] - $this->types[$current]['distance'] >= 0){
            return $this->inboundFits($rules,$current);
        }
        // Outbound
        else {
            return $this->outboundFits($rules,$current);
        }
    }

    /**
     *
     * @param array $rules
     * @param string $current
     * @return boolean
     */
    private function inboundFits ($rules,$current) {
        foreach ($rules as $rule){
            $aRule = explode('_', $rule['IBeaconCampaignProximityRule']['type']);
            if($aRule[0] == 'inbound'){
                if($aRule[1] == $this->types[$current]['name']){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     *
     * @param array $rules
     * @param string $current
     * @return boolean
     */
    private function outboundFits ($rules,$current) {
        foreach ($rules as $rule){
            $aRule = explode('_', $rule['IBeaconCampaignProximityRule']['type']);
            if($aRule[0] == 'outbound'){
                if($aRule[1] == $this->types[$current]['name']){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     *
     * @param int $campaignId
     */
    public function findByCampaignId ($campaignId) {
        return $this->find('all',array(
            'conditions' => array(
                'campaign_id' => $campaignId
            )
        ));
    }
}