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

App::uses('HmacOauth', 'ibeacon.IBeacon');

class IBeaconComponent  extends Component {

    protected $controller;


    public function initialize(\Controller $controller) {
        $controller->viewClass = 'Json';
        $this->setHeader();
        $this->controller = $controller;
        $this->authorization();
        $this->checkRequest();
        parent::initialize($controller);

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

    /**
     *
     */
    protected function authorization  ()  {
        $hmacOauth = new HmacOauth($this->controller->request);
        if(!$hmacOauth->check()){
            throw new UnauthorizedException();
        }
    }
    /**
     *
     */
    public function getUserId () {
        return HmacOauth::getUserId();
    }

    /**
     *
     * @param type $method
     * @param type $data
     */
    public function logging ($method,$data) {
        $message = "\nmethod - " . $method . "\n";
        $data['userid'] = HmacOauth::getUserId();
        if(is_array($data)){
            foreach($data as $key => $val){
                $message .= $key . ' - ' . $val ."\n";
            }
        }
        CakeLog::write('ibeacon_location',$message);
    }

}