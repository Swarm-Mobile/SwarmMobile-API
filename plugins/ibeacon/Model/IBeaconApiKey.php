<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IBeaconApiKey
 *
 * @author Zotov Maxim <zotov_mv@groupbwt.com>
 */

App::uses('IBeaconModel','ibeacon.Model');



class IBeaconApiKey extends IBeaconModel {


    /**
     *
     * @var string
     */
    public $useTable = 'ibeacon_api_key';

    /**
     *
     * @var string
     */
    public $useDbConfig = 'backstage';

    /**
     *
     * @var string
     */
    public $id = 'id';



    /**
     *
     * @param string $username
     * @return string
     */
    public function findByUsername ($username) {
        return $this->find('first',array(
            'joins' => array(
                array(
                    'alias' => 'u',
                    'table' => 'user',
                    'type' => 'INNER',
                    'conditions' => array(
                        'u.id = IBeaconApiKey.user_id',
                    ),
                )
            ),
            'conditions' => array(
                'u.username' => $username,
            )
        ));

    }
}