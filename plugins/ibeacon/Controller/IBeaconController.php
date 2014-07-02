<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IBeconController
 *
 * @author Zotov Maxim <zotov_mv@groupbwt.com>
 */

App::uses('Controller', 'Controller');

class IBeaconController extends Controller {

    public $components = array(
       'RequestHandler',
        'ibeacon.IBeacon'
    );

    public function beforeFilter() {
        parent::beforeFilter();
    }
}