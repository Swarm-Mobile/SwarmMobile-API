<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Location
 *
 * @author Zotov Maxim <zotov_mv@groupbwt.com>
 */


App::uses('IBeaconModel','ibeacon.Model');

App::uses('IBeaconSetting','ibeacon.Model');

App::uses('IBeaconLocationSetting','ibeacon.Model');


class IBeaconLocation extends IBeaconModel {

    /**
     *
     * @var string
     */
    public $useTable = 'location';

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
     * Search Lotsation for major, minor and uuid
     * @param int $major
     * @param int $minor
     * @param string $UUID
     * @return array
     */
    public function findByUUID($UUID,$major,$minor) {
        return $this->field('all',array(
            'joins' => array(
                array(
                    'alias' => 'd',
                    'table' => 'device',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'd.location_id = IBeaconLocation.id',
                    ),
                ),
                array(
                   'alias' => 'de',
                   'table' => 'deviceenvironment',
                   'type' => 'LEFT',
                   'conditions' => array(
                       'd.deviceenvironment_id = de.id',
                    )
                )
            ),
            'conditions' => array(
                'de.uuid' => $UUID,
                'd.major' => $major,
                'd.minor' => $minor
            )
        ));
    }

    /**
     * Search brands for location
     * @param int $id
     * @return array
     */
    public function findBrandById ($id) {
        $settingModel = new IBeaconSetting();
        $locationSettingModel = new IBeaconLocationSetting();
        $brandIDs = $settingModel->getBrandIds();
        echo '<pre>'.print_r($brandIDs,true).'</pre>';
        echo max($brandIDs);
        return $locationSettingModel->find('list',array(
            'conditions' => array(
                'setting_id between ? and ?' => array(min($brandIDs),max($brandIDs)),
                'location_id' => $id
            ),
            'fields' => array('value')
        ));
    }

    /**
     * Search Categor for location
     * @param int $id
     * @return array
     */
    public function findCategoryById ($id) {
        $settingModel = new IBeaconSetting();
        $locationSettingModel = new IBeaconLocationSetting();
        $categorIDs = $settingModel->getCategoryIds();
        return $locationSettingModel->find('list',array(
            'conditions' => array(
                'setting_id between ? and ?' => array(min($categorIDs),max($categorIDs)),
                'location_id' => $id
            ),
            'fields' => array('value'),
            'joins' => array(
                array(
                    'alias' => 's',
                    'table' => 'setting',
                    'type' => 'LEFT',
                    'conditions' => array(
                        's.id = IBeaconLocationSetting.setting_id',
                    )
                )
            ),
            'fields' => array('s.label','IBeaconLocationSetting.value')
        ));
    }
}