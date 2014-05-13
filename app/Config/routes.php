<?php

CakePlugin::routes();
Router::connect('/'                 , array('controller' => 'pages' , 'action' => 'display', 'home'));
Router::connect('/how_it_works'     , array('controller' => 'pages' , 'action' => 'display', 'how'));
Router::connect('/request_client'   , array('controller' => 'api'   , 'action' => 'request_client'));
Router::connect('/pages/*'          , array('controller' => 'pages' , 'action' => 'display'));
Router::connect('/users/:action'    , array('controller' => 'users'));
Router::connect('/inbox/:action'    , array('controller' => 'inbox'));
Router::connect('/logout', array('controller' => 'api', 'action' => 'logout'));
Router::connect('/login', array('controller' => 'api', 'action' => 'login'));
Router::connect('/admin', array('controller' => 'admin', 'action' => 'home'));
Router::connect('/*', array('controller' => 'api', 'action' => 'index'));
/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
require CAKE . 'Config' . DS . 'routes.php';
