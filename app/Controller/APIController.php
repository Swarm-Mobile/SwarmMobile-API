<?php

App::uses('OAuthComponent', 'OAuth.Controller/Component');
App::uses('AppController', 'Controller');
App::uses('DashboardComponent', 'Controller/Component');

class APIController extends AppController {

	public $uses = array();
	private $debug = false;

	private function _displayError($error_no, $error, $description) {
		header("Cache-Control: no-store");
		header("HTTP/1.1 $error_no");
		echo json_encode(array('error' => $error, 'error_description' => $description));
		die();
	}

	public function index() {
		header("Content-Type: application/json");
		if ($this->request->is('get')) {
			try {
				if (!$this->debug) {
					$oOAuth = new OAuthComponent();
					$oOAuth->OAuth2->verifyAccessToken($_GET['access_token']);
				}
				$path = func_get_args();
				$component = $path[0];
				$method = $path[1];
				$classname = ucfirst($component).'Component';
				if (class_exists($classname)) {
					$oComponent = new $classname();
					if (method_exists($oComponent, $method)) {
						$params = $_GET;
						unset($_GET);
						echo json_encode($oComponent->$method($_GET));
						exit();
					} else {
						$this->_displayError('404', 'endpoint_not_found', "The requested reference method don't exists");
					}
				} else {
					$this->_displayError('404', 'endpoint_not_found', "The requested reference type don't exists");
				}
			} catch (OAuth2AuthenticateException $e) {
				$e->sendHttpResponse();
				return false;
			}
		} else {
			$this->_displayError(401, 'invalid_grant', "Method Type Requested aren't granted with your access_token");
		}
	}

}
