<?php

CakePlugin::routes();
Router::connect('/'                 , array('controller' => 'pages' , 'action' => 'display', 'home'));
Router::connect('/how_it_works'     , array('controller' => 'pages' , 'action' => 'display', 'how'));
Router::connect('/request_client'   , array('controller' => 'api'   , 'action' => 'request_client'));
Router::connect('/pages/*'          , array('controller' => 'pages' , 'action' => 'display'));
Router::connect('/users/:action'    , array('controller' => 'users'));
Router::connect('/inbox/:action'    , array('controller' => 'inbox'));
$subdomain = strstr($_SERVER['HTTP_HOST'], '.', true);
switch ($subdomain) {
    case 'api':
    case 'intapi':
    case 'devapi':
    case 'newapi':
        Router::connect('/logout', array('controller' => 'api', 'action' => 'logout'));
        Router::connect('/login', array('controller' => 'api', 'action' => 'login'));
        Router::connect('/admin', array('controller' => 'admin', 'action' => 'home'));
        Router::connect('/*', array('controller' => 'api', 'action' => 'index'));
    case 'jineshapi':
        Router::connect('/logout', array('controller' => 'api', 'action' => 'logout'));
        Router::connect('/login', array('controller' => 'api', 'action' => 'login'));
        Router::connect('/admin', array('controller' => 'api', 'action' => 'home'));
        Router::connect('/users', array('controller' => 'users', 'action' => 'index'));
        Router::connect('/api/*', array('controller' => 'api', 'action' => 'index'));
        break;
    default:
        Router::connect('/api/*', array('controller' => 'api', 'action' => 'index'));
        break;
}
/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
require CAKE . 'Config' . DS . 'routes.php';
