<?php

App::uses('AppController', 'Controller');
App::uses('RollupShell', 'Console/Command');


class TestController extends AppController {

	public $uses = array();
	
	public function index(){
		set_time_limit(3600);
		$oRollup = new RollupShell();
		$oRollup->main();
		die();
	}
}
