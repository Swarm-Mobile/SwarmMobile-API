<?php

CakePlugin::routes();
Router::connect('/'                 , array('controller' => 'pages', 'action' => 'display', 'home'));
Router::connect('/pages/*'          , array('controller' => 'pages', 'action' => 'display'));
Router::connect('/users/:action'    , array('controller' => 'expMembers'));
Router::connect('/admin/:action'    , array('controller' => 'expMembers'));
Router::connect('/members/:action'  , array('controller' => 'expMembers'));
$subdomain = strstr($_SERVER['HTTP_HOST'], '.', true);
switch ($subdomain) {
	case 'api':
	case 'intapi':
	case 'devapi':
	case 'newapi':
		Router::connect('/*'		, array('controller' => 'api', 'action' => 'index'));
                break;
	case 'jineshapi':
	default:
		Router::connect('/api/*'	, array('controller' => 'api'	, 'action' => 'index'));                                     
		break;
}
require CAKE . 'Config' . DS . 'routes.php';
