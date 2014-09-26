<?php

CakePlugin::routes();
Router::connect('/'                 ,['controller' => 'pages' , 'action' => 'display', 'home']);
Router::connect('/how_it_works'     ,['controller' => 'pages' , 'action' => 'display', 'how']);
Router::connect('/request_client'   ,['controller' => 'api'   , 'action' => 'request_client']);
Router::connect('/pages/*'          ,['controller' => 'pages' , 'action' => 'display']);
Router::connect('/users/:action'    ,['controller' => 'users']);
Router::connect('/inbox/:action'    ,['controller' => 'inbox']);
Router::connect('/logout'           ,['controller' => 'api', 'action' => 'logout']);
Router::connect('/login'            ,['controller' => 'api', 'action' => 'login']);
Router::connect('/admin'            ,['controller' => 'admin', 'action' => 'home']);
Router::connect('/server_health/ok' ,['controller' => 'serverHealth', 'action' => 'ok']);


Router::connect('/customer/:id'     , ['controller' => 'Customer', 'action' => 'customer'], ['id'=>'[0-9]+']);
Router::connect('/customers'        , ['controller' => 'Customer', 'action' => 'customers']);

Router::connect('/*',['controller' => 'api', 'action' => 'index']);
/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
require CAKE . 'Config' . DS . 'routes.php';
