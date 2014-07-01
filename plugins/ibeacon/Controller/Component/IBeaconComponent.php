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
App::uses('HttpRequestRateLimit', 'Lib/IBeacon');



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

}