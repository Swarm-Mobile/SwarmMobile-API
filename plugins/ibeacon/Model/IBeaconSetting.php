<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IBeaconSettings
 *
 * @author Zotov Maxim <zotov_mv@groupbwt.com>
 */


App::uses('IBeaconModel','ibeacon.Model');

class IBeaconSetting extends IBeaconModel {

    /**
     *
     * @var string
     */
    public $useTable = 'setting';

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
     * @return array
     */
    public function getBrandIds () {
        return $this->find('list',array(
            'conditions' => array(
                'label LIKE' => '%Brand%'
            ),
            'fields' => array('id')
        ));
    }

    /**
     *
     * @return array
     */
    public function getCategoryIds () {
        return $this->find('list',array(
            'conditions' => array(
                'OR' => array(
                    'label LIKE' => '%Category%',
                    'Label LIKE' => '%NAICS Code%'
                )

            ),
            'fields' => array('id')
        ));
    }

}