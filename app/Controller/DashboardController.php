<?php

App::uses('AppController', 'Controller');
App::uses('OAuthClientComponent', 'Controller/Component');

class DashboardController extends AppController {

	public $uses = array();
	
	public function display() {
		$this->layout = 'dashboard';
		$path = func_get_args();
		$count = count($path);
		if (!$count){ return $this->redirect('/');}
		$oOAuth = new OAuthClientComponent();
		$this->set('access_token',$oOAuth->access_token);
		try	{ $this->render(implode('/', $path));	} 
		catch (MissingViewException $e) {
			if (Configure::read('debug')) { throw $e; }
			throw new NotFoundException();
		}
	}
}
