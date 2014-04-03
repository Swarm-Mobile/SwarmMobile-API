<?php

App::uses('Controller', 'Controller');

class AppController extends Controller {

	public $components = array(
		'DebugKit.Toolbar',
		'Session',
		'Auth' => array(
			'loginRedirect' => array(
				'controller' => 'pages',
				'action' => 'display',
				'home'
			),
			'logoutRedirect' => array(
				'controller' => 'pages',
				'action' => 'display',
				'home'
			)
		)
	);
	public $helpers = array(
		'Form' => array(
			'className' => 'BootstrapForm'
		)
	);
	public function beforeFilter() {
		$this->Auth->allow('index', 'display');
	}

}
