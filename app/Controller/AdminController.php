<?php

App::uses('Controller', 'Controller');
App::uses('AuthComponent', 'Controller/Component');

class AdminController extends Controller {


    public $components = array(
        'Session',
        'Auth' => array(
            AuthComponent::ALL => array(
                'userModel' => 'ExpMember',
                'loginRedirect' => array(
                    'controller' => 'admin',
                    'action' => 'index'
                ),
                'logoutRedirect' => array(
                    'controller' => 'pages',
                    'action' => 'display',
                    'home'
                )
            ),
            'authenticate' => array(
                'SwarmForm' => array(
                    'userModel' => 'ExpMember',
                    'loginRedirect' => array(
                        'controller' => 'oauth',
                        'action' => 'token'
                    ),
                    'logoutRedirect' => array(
                        'controller' => 'pages',
                        'action' => 'display',
                        'home'
                    )
                )
            )
        )
    );

    public function beforeFilter() {
        $this->Auth->allow('login', 'index', 'display');
        $this->Auth->loginRedirect = '/oauth/token';
        $this->Auth->logoutRedirect = '/';
    }
    
    public function home(){
        
    }

}
