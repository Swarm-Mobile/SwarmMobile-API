<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IBeaconCustomers
 *
 * @author Zotov Maxim <zotov_mv@groupbwt.com>
 */
App::uses('IBeaconModel','ibeacon.Model');

App::uses('IBeaconCustomerSsv','ibeacon.Model');


class IBeaconCustomers extends IBeaconModel {

/**
     *
     * @var string
     */
    public $useTable = 'ibeacon_customers';

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
    public $hasMany = array(
        'CustomerSsv' => array(
            'className' => 'ibeacon.IBeaconCustomerSsv',
            'foreignKey' => 'customer_id'
        )
    );

    /**
     * List of validation rules. It must be an array with the field name as key and using
     * as value one of the following possibilities
     * @var array
     */
    public $validate = array(
        'name' => array(
            'maxLength' => array(
                'rule' => array('maxLength', 128),
                'required'   => true
            ),
            'notEmpty'  => array(
                'rule' => array('notEmpty', 128),
                'required'   => true,
                'message' => 'Name can not be empty'
            )

        ),
        'remote_id' => array(
            'rule' => array('maxLength', 100),
            'required'   => true
        ),
        'user_id'  => array(
            'rule' => array('numeric'),
            'required'   => true,
            'on' => 'create'
        ),
        'vendor_id' => array(
            'rule' => array('maxLength',100)
        ),
        'advertiser_id' => array(
            'rule' => array('maxLength',100)
        ),
        'description' => array(
            'rule' => array('maxLength',256)
        )
    );

    /**
     *
     * @var array
     */
    public $SDKKeys = array(
        'id' => 'customerSwarmId',
        'name' => 'name',
        'description' => 'desc',
        'vendor_id' => 'vendorId',
        'advertiser_id' => 'advertiserId',
        'remote_id' => 'remoteId'
    );
    /**
     *
     * @param array $data
     */
    public function addNew ($data,$userId) {
        $customerSsv = $data['ssv'];
        unset($data['ssv']);
        $customerData = $this->SDKKeysToDB($data);
        $customerData['user_id'] = $userId;
        $customerData['ts_create'] = date('Y-m-d H:i:s');
        $customerData['ts_update'] = date('Y-m-d H:i:s');
        $this->set($customerData);
        if($this->validates()){
            $this->save();
            $this->createSsv($customerSsv,$this->id);
            return $this->id;
        }
        else{
            $errors = current ($this->validationErrors);
            //print_R($errors);
            throw new InternalErrorException($errors[0]);
        }
    }

    /**
     *
     * @param type $data
     * @return type
     * @throws InternalErrorException
     */
    public function  updateInfo ($data) {
        $customerSsv = $data['ssv'];
        unset($data['ssv']);
        $customerData = $this->SDKKeysToDB($data);
        $this->id = $customerData['id'];
        unset($customerData['id']);
        $customerData['ts_update'] = date('Y-m-d H:i:s');
        $this->set($customerData);
        if($this->validates()){
            $this->save();
            $this->updateSsv($customerSsv, $this->id);
            return $this->id;
        }
        else{
            $errors = current ($this->validationErrors);
            throw new InternalErrorException($errors[0]);
        }
    }

    /**
     * Creates a record in the table cutomer_ssv
     * @param array $ssv
     * @param integer $id
     */
    private function createSsv ($ssv,$id) {
        foreach ($ssv as $key => $val){
            foreach ($val as $k => $v){
                $ssvDB[]= array(
                    'customer_id' => $id,
                    'name' => $key,
                    'value' => $v
                );
            }
        }
        $customerSsv = new IBeaconCustomerSsv();
        $customerSsv->saveMany($ssvDB);
        //pr($customerSsv->validationErrors);
    }

    /**
     * Update  cutomer ssv
     * @param array $ssv
     * @param integer $id
     */
    private function updateSsv ($ssv,$id) {
        $customerSsv = new IBeaconCustomerSsv();
        $customerSsv->deleteAll(array('customer_id' => $id));
        return $this->createSsv($ssv, $id);
    }


    /**
     *
     * @param string $username
     * @param string $removeId
     * @return array
     */
    public function findByRemoteId ($username,$removeId) {
        return $this->find('first',array(
            'joins' => array(
                array(
                    'alias' => 'u',
                    'table' => 'user',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'u.id = IBeaconCustomers.user_id',
                    ),
                )
            ),
            'conditions' => array(
                'u.username' => $username,
                'IBeaconCustomers.remote_id' => $removeId
            )
        ));
    }
}