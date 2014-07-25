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


        /**
     *
     * @param int/string $key
     * @param mixed $default
     * @return mixed
     */
    protected  function getRawData ($key = null, $default = 0) {
        if($this->rawData === null){
            $fh = fopen('php://input', 'r');
			$content = stream_get_contents($fh);
			fclose($fh);
			$this->rawData = json_decode($content, true);
        }
        if($key !== null){
            return isset($this->rawData[$key])
                    ? $this->rawData[$key]
                    : $default;
        }
        else{
            return $this->rawData;
        }
    }
}