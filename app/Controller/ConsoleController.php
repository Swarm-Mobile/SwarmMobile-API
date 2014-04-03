<?php

App::uses('AppController', 'Controller');
App::uses('APIComponent', 'Controller/Component');

class ConsoleController extends AppController {

	public $uses = array();
	
	public function display() {
		$this->layout = 'console';
		$path = func_get_args();
		$count = count($path);
		if (!$count)	{ return $this->redirect('/');			}
		try				{ $this->render(implode('/', $path));	} 
		catch (MissingViewException $e) {
			if (Configure::read('debug')) { throw $e; }
			throw new NotFoundException();
		}
	}
}
