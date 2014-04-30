<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::uses('FormAuthenticate', 'Controller/Component/Auth');
App::uses('ExpMember', 'Model');

/**
 * Description of SwarmFormAuthenticatecd 
 */
class SwarmFormAuthenticate extends FormAuthenticate {
    protected function _findUser($username, $password = NULL) {
        $this->ExpMember = ClassRegistry::init('ExpMember');
        return $this->ExpMember->authenticate($username, $password);
    }
}