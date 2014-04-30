<?php
App::uses('Controller', 'Controller');
App::uses('AuthComponent', 'Controller/Component');

class AppController extends Controller {

	public $components = array(
		'DebugKit.Toolbar',
		'Session',
		'Auth'  => array(
            AuthComponent::ALL  => array(
                'userModel' => 'ExpMember',
                'loginRedirect'     => array(
                    'controller'    => 'api',
                    'action'        => 'admin'
                ),
                'logoutRedirect'    => array(
                    'controller'    => 'pages',
                    'action'        => 'display',
                    'home'
                )  
            ),
            'authenticate'      => array(
                'SwarmForm' =>  array ( 
                    'userModel' => 'ExpMember',
                    'loginRedirect'     => array(
                        'controller'    => 'api',
                        'action'        => 'admin'
                    ),
                    'logoutRedirect'    => array(
                        'controller'    => 'pages',
                        'action'        => 'display',
                        'home'
                    )
                )
            )
        )
	);
	public $helpers = array(
		'Form' => array(
			'className' => 'BootstrapForm'
		)
	);
	public function beforeFilter() {
		$this->Auth->allow('login', 'index', 'display');
	}

}
