<?php

CakePlugin::routes();
Router::connect('/'         , array('controller' => 'pages', 'action' => 'display', 'home'));
Router::connect('/pages/*'  , array('controller' => 'pages', 'action' => 'display'));
$subdomain = strstr($_SERVER['HTTP_HOST'], '.', true);
switch ($subdomain) {
	case 'api':
	case 'intapi':
	case 'devapi':
	case 'newapi':
		Router::connect('/login'	, array('controller' => 'api', 'action' => 'login'));
		Router::connect('/admin'	, array('controller' => 'api', 'action' => 'admin'));
		Router::connect('/*'		, array('controller' => 'api', 'action' => 'index'));
	case 'jineshapi':
		Router::connect('/api/login'	, array('controller' => 'api'	, 'action' => 'login'));
		Router::connect('/api/admin'	, array('controller' => 'api'	, 'action' => 'admin'));
		Router::connect('/users'	, array('controller' => 'users'	, 'action' => 'index'));
		Router::connect('/api/*'	, array('controller' => 'api'	, 'action' => 'index'));
		break;          
	default:
		Router::connect('/api/*'	, array('controller' => 'api'	, 'action' => 'index'));	
		break;
}
/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
require CAKE . 'Config' . DS . 'routes.php';
