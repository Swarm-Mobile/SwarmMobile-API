<?php

CakePlugin::routes();
Router::connect('/'		, array('controller' => 'test'	, 'action' => 'index'));
$subdomain = strstr($_SERVER['HTTP_HOST'], '.', true);
switch ($subdomain) {
	case 'api':
	case 'intapi':
	case 'devapi':
	case 'newapi':
		Router::connect('/*'		, array('controller' => 'api', 'action' => 'index'));                
	default:
		Router::connect('/api/*'	, array('controller' => 'api'	, 'action' => 'index'));	
		break;
}
/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
require CAKE . 'Config' . DS . 'routes.php';
