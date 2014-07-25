<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IBeaconCustomersController
 *
 * @author Zotov Maxim <zotov_mv@groupbwt.com>
 */
App::uses('IBeaconController', 'ibeacon.Controller');




class IBeaconCustomersController extends  IBeaconController  {

    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        $this->loadModel("ibeacon.IBeaconCustomers");
    }

    /**
     *
     * @throws NotFoundException
     */
    public function login () {
        $cutomerData = $this->request->data;
        $existing = $this->findCustomer($cutomerData);
        $this->IBeaconCustomers->set($existing['IBeaconCustomers']);
        if ($this->IBeaconCustomers->exists()) {
            $id = $cutomerData['customerSwarmId'] = $existing['IBeaconCustomers']['id'];
            $this->IBeaconCustomers->updateInfo($cutomerData);
        }
        else{
            $id = $this->IBeaconCustomers->addNew($cutomerData,$this->IBeacon->getUserId());
        }
        $customer = $this->IBeaconCustomers->findById($id);
        $response = $this->IBeaconCustomers->DBKeysToSDK($customer['IBeaconCustomers']);
        $response['ssv'] = $cutomerData['ssv'];
        echo json_encode($response);
        exit;
    }


    /**
     *
     * @param array $cutomerData
     * @return array
     */
    private function findCustomer ($cutomerData) {
        if(!isset($cutomerData['customerSwarmId']) || empty($cutomerData['customerSwarmId'])){
            return $this->IBeaconCustomers->findByRemoteId($cutomerData['sourceId'],$cutomerData['remoteId']);

        }
        else{
            $existing = $this->IBeaconCustomers->findById($cutomerData['customerSwarmId']);
            if(empty($existing['IBeaconCustomers'])){
                return $this->IBeaconCustomers->findByRemoteId($cutomerData['sourceId'],$cutomerData['remoteId']);
            }
        }

    }

}