<?php

App::uses('AppController', 'Controller');
App::uses('OAuthClientComponent', 'Controller/Component');

class ConsoleController extends AppController {

	public $uses = array();
	
	public function display() {
		$this->layout = 'console';
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
