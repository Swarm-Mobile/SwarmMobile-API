<?php

class AdminController extends AppController {

    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        $this->layout = 'admin';
    }
    
    public function home() {}

    public function requestClient() {
        die('request_client');
    }

    public function beforeFilter() {
        $this->Auth->allow('requestClient');
    }

}
