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
        $locationModel = new IBeaconLocation();
        $test = $locationModel->findByUUID('45FB2AE1-A73B-4ECC-852D-DB5BDFCB4F1C', 40, 2210);
        echo '<pre>'.print_R($test,true).'</pre>';
        exit;
        //$locationModel = new IBeaconLocation();
        //echo '<pre>'.print_r($locationModel->findBrandById(3),true).'</pre>';
      //  echo '<pre>'.print_r($locationModel->findCategoryById(3),true).'</pre>';
      //  exit;
    }
}