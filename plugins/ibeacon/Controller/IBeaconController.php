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

App::uses('IBeaconLocation','ibeacon.Model');

App::uses('HmacOauth', 'ibeacon.IBeacon');


class IBeaconController extends Controller {

    public $components = array(
       'RequestHandler',
        'ibeacon.IBeacon'
    );

    public function beforeFilter() {
        parent::beforeFilter();
    }

    /**
     *
     */
    public function index () {
        $HmacOauth = new HmacOauth();

        echo $HmacOauth->getHMACSignature("steve", "", "Thu, 29 Mar 2012 18:19:50", "D57092AC-DFAA-446C-8EF3-C81AA22815B5");
    //    print_R($this->request->header('Swarm-Api-Challange'));
        exit;
    }
}