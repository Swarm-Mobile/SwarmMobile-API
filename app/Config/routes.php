<?php

$subdomain = strstr($_SERVER['HTTP_HOST'], '.', true);
switch ($subdomain) {
	case 'api':
	//case 'intapi':
	case 'devapi':
	case 'newapi':
		Router::connect('/*'		, array('controller' => 'api', 'action' => 'index'));
	default:
		Router::connect('/'		, array('controller' => 'pages'	, 'action' => 'display', 'home'));
		Router::connect('/solutions'	, array('controller' => 'pages'	, 'action' => 'display', 'solutions'));
		Router::connect('/getstarted'	, array('controller' => 'pages'	, 'action' => 'display', 'getstarted'));
		Router::connect('/about'	, array('controller' => 'pages'	, 'action' => 'display', 'about'));
		Router::connect('/blog'		, array('controller' => 'pages'	, 'action' => 'display', 'blog'));
		Router::connect('/blog/*'	, array('controller' => 'pages'	, 'action' => 'display', 'article'));
		Router::connect('/api/*'	, array('controller' => 'api'	, 'action' => 'index'));
		
		Router::connect('/console'	, array('controller' => 'console', 'action' => 'display', 'home'));
		Router::connect('/console/*'	, array('controller' => 'console', 'action' => 'display'));
		
		Router::connect('/dashboard'	, array('controller' => 'dashboard', 'action' => 'display', 'dashboard'));
		Router::connect('/dashboard/*'	, array('controller' => 'dashboard', 'action' => 'display'));
				
		break;
}
CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
require CAKE . 'Config' . DS . 'routes.php';
