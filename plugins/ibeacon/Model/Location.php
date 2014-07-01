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
class Location extends IBeaconModel {

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
}