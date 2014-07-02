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
        if(isset($cutomerData['customerSwarmId']) && $cutomerData['customerSwarmId'] == 0){
            $id = $this->IBeaconCustomers->addNew($cutomerData);
        }
        else if(isset($cutomerData['customerSwarmId'])){
            $id = $cutomerData['customerSwarmId'];
            $this->IBeaconCustomers->id = $id;
            if (!$this->IBeaconCustomers->exists()) {
                throw new NotFoundException(__('Could not find that customer'));
            }
            $this->IBeaconCustomers->updateInfo($cutomerData);
        }
        $customer = $this->IBeaconCustomers->findById($id);
        $response = $this->IBeaconCustomers->DBKeysToSDK($customer['IBeaconCustomers']);
        $response['ssv'] = $cutomerData['ssv'];
        echo json_encode($response);
        exit;
    }

}