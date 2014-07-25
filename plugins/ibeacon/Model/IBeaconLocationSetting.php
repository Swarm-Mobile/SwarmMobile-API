<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IBeaconLocationSetting
 *
 * @author Zotov Maxim <zotov_mv@groupbwt.com>
 */


App::uses('IBeaconModel','ibeacon.Model');


class IBeaconLocationSetting extends IBeaconModel {

    /**
     *
     * @var string
     */
    public $useTable = 'location_setting';

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
}