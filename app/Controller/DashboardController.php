<?php

App::uses('AppController', 'Controller');
App::uses('APIComponent', 'Controller/Component');

class DashboardController extends AppController {

	public $uses = array();
	
	public function display() {
		$this->layout = 'dashboard';
		$path = func_get_args();
		$count = count($path);
		if (!$count){ return $this->redirect('/');}
		$oAPI = new APIComponent();
		$this->set('access_token',$oAPI->access_token);
		try	{ $this->render(implode('/', $path));	} 
		catch (MissingViewException $e) {
			if (Configure::read('debug')) { throw $e; }
			throw new NotFoundException();
		}
	}
	
	
}
