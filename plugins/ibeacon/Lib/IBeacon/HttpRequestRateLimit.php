<?php

/**
 * Description of HttpRequestRateLimit
 *
 * @author Zotov Maxim <zotov_mv@groupbwt.com>
 */

APP::import('Model', 'ibeacon.IBeaconRestApiRateLimit');

APP::import('Model', 'ibeacon.IBeaconBlacklistIp');


class HttpRequestRateLimit  {

    protected $settings = null;

    protected $currentIp = null;

    protected $blackListIP = null;

    protected $currentApiKey = null;

    public $lastError;

    /**
     *
     */
    public function __construct() {
        $this->settings = $this->getSettings();
        $this->blackListIP = $this->getBlackListIP();
        $this->initCache();
    }


    /**
     *
     * @param string $key
     */
    public function setApiKey ($key) {
        $this->currentApiKey = $key;
    }

    /**
     *
     * @param string $ip
     */
    public function setIp ($ip) {
        $this->currentIp = $ip;
    }


    /**
     *
     * @param string $name
     * @param string $type
     * @return string
     */
    protected function createCacheKey ($name,$type) {
        return str_replace(" ", "_", $name) . '_' .$type;
    }

    /**
     *
     */
    protected function initCache() {
        foreach ($this->settings as $key => &$setting){
            $rateLimit = $setting['RestApiRateLimit'];
            $cacheKey = $this->createCacheKey($rateLimit['name'], $rateLimit['type']);
            Cache::config($cacheKey, array(
                'engine' => "File",
                'prefix' => 'rate_limit_',
                'path' => CACHE . 'rest_api' . DS,
                'serialize' => true,
                'duration' => '+ ' . $rateLimit['period']
            ));
            $setting['RestApiRateLimit']['cache_key'] = $cacheKey;
        }
    }



    /**
     *
     * @return boolean
     */
    public function tooMany () {
        foreach ($this->settings as $setting){
            $rateLimit = $setting['RestApiRateLimit'];
            if($rateLimit['type'] == 'api_key'){
                if($this->checkByApiKey($rateLimit)){
                    return true;
                }
            }
            else if ($rateLimit['type'] == 'ip') {
                if($this->checkByIP($rateLimit)){
                    return true;
                }
            }
        }
        return false;
    }
    /**
     *
     * @return boolean
     */
    public function checkBannedIP () {
       if(in_array($this->currentIp,$this->blackListIP)){
           return true;
       }
       else{
           return false;
       }
    }

    /**
     *
     * @return boolean
     */
    protected function checkByApiKey($rateLimit) {
        if($this->currentApiKey === null){
            return false;
        }
        $value = Cache::read($this->currentApiKey,$rateLimit['cache_key']);
        if($value >= $rateLimit['max_number_requests']){
            $this->lastError = $rateLimit['message'];
            return true;
        }
        Cache::write($this->currentApiKey,$value + 1, $rateLimit['cache_key']);

    }

    /**
     *
     * @return boolean
     */
    protected function checkByIP ($rateLimit) {
        if($this->currentIp === null){
            return false;
        }
        $value = Cache::read($this->currentIp,$rateLimit['cache_key']);
        if($value >= $rateLimit['max_number_requests']){
            $this->lastError = $rateLimit['message'];
            return true;
        }
        Cache::write($this->currentIp,$value + 1, $rateLimit['cache_key']);
    }

    /**
     *
     * @return array()
     */
    public function getSettings () {
        if($this->settings === null){
            $setings = Cache::read('rest_api_rate_limit',  "_cake_core_");
            if(!is_array($setings)){
                $model = new IBeaconRestApiRateLimit();
                $setings = $model->find("all");
                Cache::write('rest_api_rate_limit',$setings);
            }
            $this->settings = $setings;
        }
        return $this->settings;
    }

    /**
     *
     * @return array()
     */
    public function getBlackListIP (){
        if($this->blackListIP === null){
            $blackListIP  = Cache::read('rest_api_black_list_api',  "_cake_core_");
            if(!is_array($blackListIP)){
                $model = new IBeaconBlacklistIp();
                $blackListIP = $model->find("list",array(
                    'fields' => array('IBeaconBlacklistIp.ip')
                    )
                );
                Cache::write('rest_api_black_list_api',$blackListIP,  "_cake_core_");
            }
            $this->blackListIP = $blackListIP;
        }
        return $this->blackListIP;
    }
}
