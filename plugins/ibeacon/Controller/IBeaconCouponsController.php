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
App::uses('RedisComponent', 'ibeacon.Controller/Component');

App::uses('IBeaconCampaign', 'ibeacon.Model');
App::uses('IBeaconCustomers', 'ibeacon.Model');
App::uses('IBeaconLocation', 'ibeacon.Model');
App::uses('IBeaconCouponConfiguration', 'ibeacon.Model');
App::uses('IBeaconCampaignProximityRule', 'ibeacon.Model');
App::uses('IBeaconDeviceCoordinate', 'ibeacon.Model');
App::uses('User', 'ibeacon.Model/PingAsPresence');



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
        $cupon = $this->IBeaconCoupons->find('first',array('conditions' => array('IBeaconCoupons.id' => $id)));
        $couponConfigurationModel = new IBeaconCouponConfiguration();
        $configuration = $couponConfigurationModel->find('first', array(
            'conditions' => array(
               'campaign_id' => $cupon['IBeaconCoupons']['campaign_id'],
            )
        ));
        $response = array_merge($configuration['IBeaconCouponConfiguration'],$cupon['IBeaconCoupons']);
        $responseSDK = $this->IBeaconCoupons->DBKeysToSDK($response);
        $responseSDK['endDate'] = $cupon['campaign']['end_date'];
        echo json_encode($responseSDK);
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
        $responseSDK['endDate'] = $coupon['campaign']['end_date'];
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
        $deviceCoordinateModel = new IBeaconDeviceCoordinate();
        $response = array();
        foreach ($LocationIdentifierList as $LocationIdentifier){
            $LocationIdentifier['customerSwarmId'] = $_GET['userid'];
            $this->IBeacon->logging('whatIsHere',  array_merge($LocationIdentifier,$_GET));
            $locations = $locationModel->findByUUID(
                    $LocationIdentifier['uuid'],
                    $LocationIdentifier['major'],
                    $LocationIdentifier['minor']
            );
            foreach ($locations as $location){
                if(isset($location['IBeaconLocation']) && !empty($location['IBeaconLocation'])) {
                    if(!empty($LocationIdentifier['latitude']) && !empty($LocationIdentifier['longitude']) && !empty($location['d']['id'])) {
                        $deviceCoordinateModel->addNew(
                                $LocationIdentifier['latitude'],
                                $LocationIdentifier['longitude'],
                                $location['d']['id']
                        );
                    }
                    $deviceId = $location['d']['id'];
                    unset($location['d']['id']);
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
                    $response['coupons'] = array();
                    $cuponModel =  new IBeaconCoupons();
                    foreach($campaigns as $campaign){
                        $response['coupons']  = array_merge($response['coupons'] ,$cuponModel->findByCustomerIdAndCampaignId($customer['IBeaconCustomers']['id'],$campaign['id']));
                         //$response['coupons'][] = $cuponModel->findByCustomerIdAndCampaignId($customer['IBeaconCustomers']['id'],$campaign['id']);
                    }
                    $this->pingAsPresence($location['id'], $deviceId);
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
                //TODO calculateS score
                /*$minimumScore = empty($campaign['IBeaconCampaign']['minimum_score'])
                                    ? 0
                                    : $campaign['IBeaconCampaign']['minimum_score'];*/

                $suitableCampaigns[] = $campaign['IBeaconCampaign'];
            }
        }
        return $suitableCampaigns;
    }

    /**
     * Ping as Presence
     * 
     * Inserts data into redis
     * @param Integer locationId
     * @param Integer deviceId
     */
    private function pingAsPresence($locationId, $deviceId)
    {
        $email = $this->request->data['email_id'];
        $lat   = (!empty($this->request->data['locations'][0]['latitude'])) ? $this->request->data['locations'][0]['latitude'] : '';
        $long  = (!empty($this->request->data['locations'][0]['longitude'])) ? $this->request->data['locations'][0]['longitude'] : '';
        $rssi  = (!empty($this->request->data['locations'][0]['rssi'])) ? $this->request->data['locations'][0]['rssi'] : '';
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if(empty($email)) return FALSE;
        $userModel = new User();
        $user = $userModel->findByEmail($email);
        if(empty($user)) {
            $userModel->set('email', $email);
            $user = $userModel->save();
        }
        try {
            $redis = RedisComponent::getInstance('pingAsPresence');
        } catch (Exception $e) {
            return FALSE;
        }
        if(empty($locationId)) return;
        $hKey = $locationId . ':' . $user['User']['id'];
        $hDeviceKey  = 'device:' . $deviceId;
        $data = $redis->hgetall($hKey);
        $date = new DateTime('now', new DateTimeZone('GMT'));
        $currentTime = $date->format('Y-m-d H:i:s');
        $currentTimestamp = $date->getTimestamp();
        $dataSet = array();
        if (!empty($data['timestamp'])) {
            $timeDiff = round(abs(strtotime($currentTime) - strtotime($data['timestamp'])) / 60, 2);
            if ($timeDiff < 20) {
                $dataSet[$hDeviceKey] = (!empty($data[$hDeviceKey])) ? $data[$hDeviceKey] . '$' . $currentTimestamp : $currentTimestamp;
                $dataSet['timestamp'] = $currentTime;
                $dataSet[$hDeviceKey] .= ':' . $lat . ':' . $long . ':' . $rssi;
                $redis->hmset($hKey, $dataSet);
                return TRUE;
            } else {
                if (empty($data['count'])) {
                    $dataSet['count'] = 1;
                    $dataSet['timestamp-1'] = $currentTime;
                    $hDeviceKey = $hDeviceKey . '-1';
                    $dataSet[$hDeviceKey] = $currentTimestamp;
                    $dataSet[$hDeviceKey] .= ':' . $lat . ':' . $long . ':' . $rssi;
                    $redis->hmset($hKey, $dataSet);
                    return TRUE;
                } else {
                    $timeDiff = round(abs(strtotime($currentTime) - strtotime($data['timestamp-' . $data['count']])) / 60, 2);
                    if ($timeDiff < 20) {
                        $hDeviceKey = $hDeviceKey . '-' . $data['count'];
                        $dataSet[$hDeviceKey] = (!empty($data[$hDeviceKey])) ? $data[$hDeviceKey] . '$' . $currentTimestamp : $currentTimestamp;
                        $dataSet['timestamp-' . $data['count']] = $currentTime;
                        $dataSet[$hDeviceKey] .= ':' . $lat . ':' . $long . ':' . $rssi;
                        $redis->hmset($hKey, $dataSet);
                        return;
                    } else {
                        $dataSet['count'] = $data['count'] + 1;
                        $hDeviceKey = $hDeviceKey . '-' . $dataSet['count'];
                        $dataSet[$hDeviceKey] = $currentTimestamp;
                        $dataSet['timestamp-' . $dataSet['count']] = $currentTime;
                        $dataSet[$hDeviceKey] .= ':' . $lat . ':' . $long . ':' . $rssi;
                        $redis->hmset($hKey, $dataSet);
                        return TRUE;
                    }
                }
            }
        }
        $dataSet['timestamp'] = $currentTime;
        $dataSet[$hDeviceKey] = $currentTimestamp;
        $dataSet[$hDeviceKey] .= ':' . $lat . ':' . $long . ':' . $rssi;
        $redis->hmset($hKey, $dataSet);
        return TRUE;
    }
}
