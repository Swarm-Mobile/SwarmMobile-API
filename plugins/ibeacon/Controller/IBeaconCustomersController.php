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

App::uses('IBeconController', 'Controller');


class IBeaconCustomersController extends  IBeconController  {

    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        $this->loadModel("Customers");
    }

    /**
     *
     * @throws NotFoundException
     */
    public function login () {
        $cutomerData = $this->request->data;
        if(isset($cutomerData['customerSwarmId']) && $cutomerData['customerSwarmId'] == 0){
            $id = $this->Customers->addNew($cutomerData);
        }
        else if(isset($cutomerData['customerSwarmId'])){
            $id = $cutomerData['customerSwarmId'];
            $this->Customers->id = $id;
            if (!$this->Customers->exists()) {
                throw new NotFoundException(__('Could not find that customer'));
            }
            $this->Customers->updateInfo($cutomerData);
        }
        $customer = $this->Customers->findById($id);
        $response = $this->Customers->DBKeysToSDK($customer['Customers']);
        $response['ssv'] = $cutomerData['ssv'];
        echo json_encode($response);
        exit;
    }

}