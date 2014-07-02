<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IBeacon
 *
 * @author Zotov Maxim <zotov_mv@groupbwt.com>
 */

App::uses('Model', 'Model');


class IBeaconModel extends Model {


    /**
     *
     * @var array
     */
    public $SDKKeys = array();

    /**
     *
     * @param array $attributes
     * @return array
     */
    public function SDKKeysToDB ($attributes) {
      //  print_R($attributes);exit;
        if(empty($this->SDKKeys) || empty($attributes)){
            return $attributes;
        }
        $trans = array_flip($this->SDKKeys);
        $dbKeys = array();
        foreach ($attributes as $key => $val){
            if(isset($trans[$key])){
                $dbKeys[$trans[$key]] = $val;
            }
        }
        return $dbKeys;
    }
    /**
     *
     * @param array $attributes
     * @return array
     */
    public function DBKeysToSDK ($attributes) {
        if(empty($this->SDKKeys) || empty($attributes)){
            return $attributes;
        }
        $dbKeys = array();
        foreach ($attributes as $key => $val){
            if(isset($this->SDKKeys[$key])){
                $dbKeys[$this->SDKKeys[$key]] = $val;
            }
        }
        return $dbKeys;
    }
}