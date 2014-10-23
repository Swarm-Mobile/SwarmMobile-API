<?php

CakePlugin::routes();
//Router::connect('/'                 ,['controller' => 'pages' , 'action' => 'display', 'home']);
//Router::connect('/how_it_works'     ,['controller' => 'pages' , 'action' => 'display', 'how']);
Router::connect('/request_client'   ,['controller' => 'api'   , 'action' => 'request_client']);
//Router::connect('/pages/*'          ,['controller' => 'pages' , 'action' => 'display']);
//Router::connect('/users/:action'    ,['controller' => 'users']);
//Router::connect('/inbox/:action'    ,['controller' => 'inbox']);
Router::connect('/logout'           ,['controller' => 'api', 'action' => 'logout']);
Router::connect('/login'            ,['controller' => 'api', 'action' => 'login']);
Router::connect('/admin'            ,['controller' => 'admin', 'action' => 'home']);
Router::connect('/server_health/ok' ,['controller' => 'serverHealth', 'action' => 'ok']);


Router::connect('/customer'            , ['controller' => 'Customer', 'action' => 'customer']);
Router::connect('/customers'           , ['controller' => 'Customer', 'action' => 'customers']);
Router::connect('/brands'              , ['controller' => 'Brand'   , 'action' => 'brands']);
Router::connect('/categories'          , ['controller' => 'Category', 'action' => 'categories']);
Router::connect('/location/highlights' , ['controller' => 'Location', 'action' => 'highlights']);

Router::connect('/*',['controller' => 'api', 'action' => 'index']);
/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
require CAKE . 'Config' . DS . 'routes.php';
