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

class IBeconController {

    public $components = array(
        'RequestHandler',
        'DebugKit.Toolbar',
        'IBeacon'
    );

    public function beforeFilter() {
        parent::beforeFilter();
    }
}