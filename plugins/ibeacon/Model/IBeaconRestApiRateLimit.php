<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IBeaconRestApiRateLimit
 *
 * @author Zotov Maxim <zotov_mv@groupbwt.com>
 */
class IBeaconRestApiRateLimit extends IBeaconModel {

    /**
     *
     * @var string
     */
    public $useDbConfig = 'backstage';
    /**
     *
     * @var string
     */
    public $useTable = 'ibeacon_rest_api_rate_limit';

}