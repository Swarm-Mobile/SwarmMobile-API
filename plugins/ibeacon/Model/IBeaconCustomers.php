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
            'className' => 'IBeaconCustomerSsv',
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
    public function addNew ($data) {
        $customerSsv = $data['ssv'];
        unset($data['ssv']);
        $customerData = $this->SDKKeysToDB($data);
        //TODO get userId;
        $customerData['user_id'] = 1;
        $this->set($customerData);
        if($this->validates()){
            $this->save();
            $this->createSsv($customerSsv,$this->id);
            return $this->id;
        }
        else{
            $errors = current ($this->validationErrors);
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
            $ssvDB[]= array(
                'customer_id' => $id,
                'name' => $key,
                // TODO понять там масив занчении???
                'value' => $val[0]
            );
        }
        $customerSsv = new CustomerSsv();
        $customerSsv->saveMany($ssvDB);
        //pr($customerSsv->validationErrors);
    }
    /**
     * Update  cutomer ssv
     * @param array $ssv
     * @param integer $id
     */
    private function updateSsv ($ssv,$id) {
        $customerSsv = new CustomerSsv();
        foreach ($ssv as $key => $val){
            $exists = $customerSsv->find('first',array(
                'conditions' => array(
                    'customer_id' => $id,
                    'name' => $key
                )
            ));
            if(is_array($exists) && isset($exists['CustomerSsv'])){
                // TODO понять там масив занчении???
                if($exists['CustomerSsv']['value'] != $val[0]){
                    $customerSsv->set($exists['CustomerSsv']);
                    $customerSsv->set(array('value' => $val[0]));
                    $customerSsv->save();
                    //pr($customerSsv->validationErrors);
                }
            }
            else{
                $customerSsv->save(array(
                    'customer_id' => $id,
                    'name' => $key,
                    'value' => $val[0]
                ));
                //pr($customerSsv->validationErrors);
            }
        }
    }
}