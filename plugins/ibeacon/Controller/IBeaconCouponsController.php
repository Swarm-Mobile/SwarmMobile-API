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


App::uses('IBeconController', 'Controller');

App::uses("IBeaconCampaign", "Model");

App::uses("IBeaconCouponConfiguration", "Model");

class IBeaconCouponsController  extends IBeconController {



    /**
     *
     * @param type $request
     * @param type $response
     */
    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        $this->loadModel('Coupons');
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
     * @param integer $id
     */
    public function confirmation ($id) {
        if(!$this->Coupons->exists($id)){
            throw new NotFoundException(__('Could not find that coupon'));
        }
        // TODO узнать что такое userId
        $data = $this->request->data;
        $this->Coupons->confirmation($id,$data['action']);
    }
    /**
     *
     * @param integer $campaigning
     */
    public function createByCampaigningId ($campaigningId) {
        $campaignModel =  new Campaigns();
        $cutomerId = isset($this->params['url']['userid']) ? $this->params['url']['userid'] : null;
        $exists = (bool)$campaignModel->find("active",array(
            'conditions' => array(
               'Campaigns.id' => $campaigningId,
            )
        ));
        if(!$exists){
            throw new NotFoundException(__('Could not find that campaigning'));
        }
        $id = $this->Coupons->createNew($campaigningId,$cutomerId);
        $coupon = $this->Coupons->findById($id);
        $couponConfigurationModel = new CouponConfiguration();
        $configuration = $couponConfigurationModel->find('first', array(
            'conditions' => array(
               'campaign_id' => $campaigningId,
            )
        ));
        $response = array_merge($coupon['Coupons'],$configuration['CouponConfiguration']);
        $responseSDK = $this->Coupons->DBKeysToSDK($response);
        echo json_encode($responseSDK);
        exit;

    }
}