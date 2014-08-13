<?php
App::uses('AppController', 'Controller');
/**
 * Class to monitor server health 
 */
class ServerHealthController extends AppController
{
    /**
     * 
     * @return String
     */
    public function ok() 
    {
        echo 'Server Health Success.';
        exit();
    }
    
    public function beforeFilter() 
    {
        parent::beforeFilter();
        $this->Auth->allow(
            'ok'
        );
    }
}