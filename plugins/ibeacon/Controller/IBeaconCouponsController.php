<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IBeaconCouponsController
 *
 * @author Zotov Maxim <zotov_mv@groupbwt.com>
 */


App::uses('IBeaconController', 'ibeacon.Controller');

App::uses('IBeaconCampaign', 'ibeacon.Model');

App::uses('IBeaconCustomers', 'ibeacon.Model');

App::uses('IBeaconLocation', 'ibeacon.Model');

App::uses('IBeaconCouponConfiguration', 'ibeacon.Model');

App::uses('IBeaconCampaignProximityRule', 'ibeacon.Model');



class IBeaconCouponsController  extends IBeaconController {


    /**
     *
     * @param type $request
     * @param type $response
     */
    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
       $this->loadModel('ibeacon.IBeaconCoupons');
    }

    /**
     *
     */
    public function findByCutomerId () {
        $cutomerId = isset($this->params['url']['userid']) ? $this->params['url']['userid'] : 0;
        $cupons = $this->Coupons->find("all" , array(
            'conditions' => array(
                'customer_id' => $cutomerId
            )
        ));
        $this->set(array(
            'cupons' => $cupons,
            '_serialize' => array('cupons')
        ));
    }
    /**
     * Proof of status
     * @param int $id
     */
    public function reactToCoupon ($id) {
        if(!$this->IBeaconCoupons->exists($id)){
            throw new NotFoundException(__('Could not find that coupon'));
        }
        // TODO узнать что такое userId
        $data = $this->request->data;
        $this->IBeaconCoupons->confirmation($id,$data['action']);
        exit;
    }
    /**
     *
     * @param int $campaigning
     */
    public function couponForCampaign ($campaigningId) {
        $campaignModel =  new IBeaconCampaign();
        $cutomerId = isset($this->params['url']['userid']) ? $this->params['url']['userid'] : null;
        $exists = (bool)$campaignModel->find("active",array(
            'conditions' => array(
               'IBeaconCampaign.id' => $campaigningId,
            )
        ));
        if(!$exists){
            throw new NotFoundException(__('Could not find that campaigning'));
        }
        $id = $this->IBeaconCoupons->createNew($campaigningId,$cutomerId);
        $coupon = $this->IBeaconCoupons->findById($id);
        $couponConfigurationModel = new IBeaconCouponConfiguration();
        $configuration = $couponConfigurationModel->find('first', array(
            'conditions' => array(
               'campaign_id' => $campaigningId,
            )
        ));
        $response = array_merge($configuration['IBeaconCouponConfiguration'],$coupon['IBeaconCoupons']);
        $responseSDK = $this->IBeaconCoupons->DBKeysToSDK($response);
        echo json_encode($responseSDK);
        exit;
    }
    /**
     *
     */
    public function whatIsHere () {
        $custmerModel = new IBeaconCustomers();
        $customerId = $_GET['userid'];
        $customer = $custmerModel->findById($customerId);
        if(!isset($customer['IBeaconCustomers']) || empty($customer['IBeaconCustomers'])){
            throw new NotFoundException(__('Could not find that cutomer'));
        }
        $LocationIdentifierList = $this->request->data['locations'];
        $response = $this->locationIdentifiers($LocationIdentifierList,$customer);
        echo json_encode($response);
        exit;
    }

    /**
     *
     * @param array $LocationIdentifierList
     * @param array $customer
     * @return array
     */
    private function locationIdentifiers ($LocationIdentifierList,$customer) {
        $locationModel = new IBeaconLocation();
        $response = array();
        foreach ($LocationIdentifierList as $LocationIdentifier){
            $locations = $locationModel->findByUUID(
                    $LocationIdentifier['uuid'],
                    $LocationIdentifier['major'],
                    $LocationIdentifier['minor']
            );
            foreach ($locations as $location){
                if(isset($location['IBeaconLocation']) && !empty($location['IBeaconLocation'])){
                    //TODO update coordinates
                    $location = array_merge($LocationIdentifier,$location['IBeaconLocation']);
                    $brands = $locationModel->findBrandById($location['id']);
                    $categorys = $locationModel->findCategoryById($location['id']);
                    $campaigns = $this->campaignIdentifiers($location,$customer);
                    $location['brands'] = array(
                        'list' => $brands
                    );
                    $location['categorization'] = $categorys;
                    $response['locations'][] = $location;
                    $response['campaigns'] = $campaigns;
                    /*if(!empty()){

                    }*/
                }
            }

        }
        return $response;
    }


    /**
     *
     * @param array $location
     * @param array $customer
     */
    private function campaignIdentifiers ($location,$customer) {
        $campaignModel =  new IBeaconCampaign();
        $campaignProximityRuleModel =  new IBeaconCampaignProximityRule();
        $suitableCampaigns = array();
        $campaigns = $campaignModel->find('active',array(
            'conditions' => array(
                'location_id' => $location['id']
            )
        ));
        foreach ($campaigns as $campaign){
            $campaignId = $campaign['IBeaconCampaign']['id'];
            $countUsedCoupons = $this->IBeaconCoupons->getCountUsedByCampaignId($campaignId);
            if($countUsedCoupons >= $campaign['IBeaconCampaign']['total_coupons']){
                continue;
            }
            $previous = $location['prex'];
            $current = $location['pr'];
            if($campaignProximityRuleModel->fits($campaignId, $previous, $current)){
                //TODO calculateS core
                /*$minimumScore = empty($campaign['IBeaconCampaign']['minimum_score'])
                                    ? 0
                                    : $campaign['IBeaconCampaign']['minimum_score'];*/

                $suitableCampaigns[] = $campaign['IBeaconCampaign'];
            }
        }
        return $suitableCampaigns;
    }
}