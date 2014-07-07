<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HmacOauth
 *
 * @author Zotov Maxim <zotov_mv@groupbwt.com>
 */

APP::import('Model', 'ibeacon.IBeaconApiKey');

class HmacOauth {

    const ALGORITHM = 'sha256';
    const HEADER_SWARM_SIGNATURE = "Swarm-Api-Challange";
    const HEADER_SWARM_TIMESTAMP = "Swarm-Timestamp";
    const HEADER_SWARM_PARTNER_ID = "Swarm-Partner-Id";
    const HEADER_SWARM_REMOTE_ID = "Swarm-Remote-Id";

    /**
     *
     * @var string
     */
    private $date;

    /**
     *
     * @var string
     */
    private $remoteId;


    /**
     *
     * @var string
     */
    private $partnerId;

    /**
     *
     * @var type
     */
    private $key;

    /**
     *
     * @var int
     */
    private static $userId;

    /**
     *
     * @param CakeRequest $request
     */
    public function __construct($request = null) {
        if($request !== null){
            $this->date         = $request->header(HmacOauth::HEADER_SWARM_TIMESTAMP);
            $this->remoteId     = $request->header(HmacOauth::HEADER_SWARM_REMOTE_ID);
            $this->partnerId    = $request->header(HmacOauth::HEADER_SWARM_PARTNER_ID);
            $this->key          = $request->header(HmacOauth::HEADER_SWARM_SIGNATURE);
        }
    }

    /**
     *
     */
    public function check () {
        $modelApiKey = new IBeaconApiKey();
        $aApiKey = $modelApiKey->findByUsername($this->partnerId);
        self::$userId = $aApiKey['IBeaconApiKey']['user_id'];
        $signatur = $this->getHMACSignature($this->partnerId, $this->remoteId, $this->date , $aApiKey['IBeaconApiKey']['key']);
        if($signatur == $this->key ){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     *
     * @param string $partnerId
     * @param string $remoteId
     * @param string $date
     * @param string $key
     * @return string
     */
    protected function getHMACSignature ($partnerId, $remoteId, $date, $key) {
        if(empty($remoteId)){
            $remoteId = "none";
        }
        $stepOne = base64_encode( hash_hmac(HmacOauth::ALGORITHM, $partnerId, $key, true) );
        $stepTwo = base64_encode ( hash_hmac(HmacOauth::ALGORITHM, $stepOne, $remoteId, true) );
        $stepThree = base64_encode( hash_hmac(HmacOauth::ALGORITHM, $stepTwo, $date, true) );
        return $stepThree;
    }

    public static function getUserId () {
        return self::$userId;
    }
}