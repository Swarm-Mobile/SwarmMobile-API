<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IBeaconComponent
 *
 * @author Zotov Maxim <zotov_mv@groupbwt.com>
 */
App::uses('HttpRequestRateLimit', 'ibeacon.IBeacon');



class IBeaconComponent  extends Component {

    protected $controller;


    public function initialize(\Controller $controller) {
        $this->controller = $controller;
        $this->checkRequest();
        parent::initialize($controller);
        $controller->viewClass = 'Json';
    }

    /**
     *
     */
    protected function checkRequest (){
        $ratelimit = new HttpRequestRateLimit();
        $ratelimit->setIp($this->controller->request->clientIp());
        if($ratelimit->checkBannedIP()){
            throw new ForbiddenException("ip address is blacklisted");
        }
        if($ratelimit->tooMany()){
            throw new ForbiddenException($ratelimit->lastError);
        }
    }
    /**
     *
     */
    protected function setHeader () {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET");
        header("Access-Control-Allow-Headers: X-PINGOTHER");
        header("Access-Control-Max-Age: 1728000");
        header("Content-Type: application/json; charset=UTF-8");
    }

}