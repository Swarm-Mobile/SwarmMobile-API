<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IBeaconCoupons
 *
 * @author Zotov Maxim <zotov_mv@groupbwt.com>
 */

App::uses("UUID", "ibeacon.Lib");

App::uses('IBeaconModel','ibeacon.Model');

class IBeaconCoupons extends IBeaconModel {


    /**
     *
     * @var string
     */
    public $useTable = 'ibeacon_coupons';

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

    public $belongsTo = array(
        'customer' => array(
            'className' => 'ibeacon.IBeaconCustomers',
            'foreignKey' => 'customer_id',
        ),
        'campaign' => array(
            'className' => 'ibeacon.IBeaconCampaign',
            'foreignKey' => 'campaign_id'
        )
    );

    /**
     * List of validation rules. It must be an array with the field name as key and using
     * as value one of the following possibilities
     * @var array
     */
    public $validate = array(
        'campaign_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'required' => true,
            )
        ),
        'customer_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'required' => true,
            ),
            'notEmpty' => array(
                'rule' => array('notEmpty'),
                'required' => true,
            )
        ),
        'code' => array(
            'maxLength' => array(
                'rule' => array('maxLength',100)
            )
        ),
        'status' => array(
            'maxLength' => array(
                'rule' => array('inList',array(
                    'new',
                    'accept',
                    'reject'
                ))
            )
        )
    );
    /**
     *
     * @var array
     */
    public $SDKKeys = array(
        'id' => 'id',
        'image_url' => 'imageUrl',
        'external_url' => 'externalUrl',
        'title' => 'title',
        'text' => 'text',
        'code' => 'code',
        'status' => 'status',
        'delivery_text' => 'deliveryText'
    );
    /**
     *
     * @param integer $id
     * @param string $status
     */
    public function confirmation ($id,$status) {
        $this->read(null,$id);
        $this->save(array("status" => $status));
    }

    /**
     *
     * @param integer $campaigningId
     * @param integer $cutomerId
     */
    public function createNew ($campaigningId,$cutomerId){
        $code = UUID::v4();
        $this->set(array(
            'customer_id' => $cutomerId,
            'campaign_id' => $campaigningId,
            'code' => $code,
            'status' => 'new',
            'ts_create' => date('Y-m-d H:i:s'),
            'ts_update' => date('Y-m-d H:i:s')
        ));
        if($this->validates()){
            $this->save();
            return $this->id;
        }
        else{
            $errors = current ($this->validationErrors);
            throw new InternalErrorException($errors[0]);
        }
    }

    /**
     * considers the number of used
     * @param int $campaignId
     * @return int
     */
    public function getCountUsedByCampaignId ($campaignId) {
        return $this->find("count",array(
            'conditions' => array(
                'campaign_id' => $campaignId,
                'status' => 'accept'
            )
        ));
    }
}