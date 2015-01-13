<?php
/**
 * This file is loaded automatically by the app/webroot/index.php file after core.php
 *
 * This file should load/create any application wide configuration settings, such as
 * Caching, Logging, loading additional configuration files.
 *
 * You should also use this file to include any files that provide global functions/constants
 * that your application uses.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.10.8.2117
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
// Start Composer Include
    require APP . '../vendor/autoload.php';

    /**
     * Remove and re-prepend CakePHP's autoloader as Composer thinks it is the most important.
     * See: http://goo.gl/kKVJO7
     */
    spl_autoload_unregister(['App', 'load']);
    spl_autoload_register(['App', 'load'], true, true);
// End Composer Include
    
//Import Exceptions
require_once(APP.'/Lib/Error/Exception.php');
foreach (glob(APP."/Lib/Error/*.php") as $filename) require_once($filename);
unset($filename);

// Setup a 'default' cache configuration for use in the application.
Cache::config('default', [
    'engine' => 'File',
    'mask'   => 0777,
]);

//Create additional App:uses() folders
App::build([
    'Model/Device'   => [ ROOT . '/app/Model/Device'],
    'Model/Location' => [ ROOT . '/app/Model/Location'],
    'Model/Metrics'  => [ ROOT . '/app/Model/Metrics'],
    'Model/POS'      => [ ROOT . '/app/Model/POS'],
    'Model/Ping'     => [ ROOT . '/app/Model/Ping'],
    'Model/Portal'   => [ ROOT . '/app/Model/Portal'],
    'Model/User'     => [ ROOT . '/app/Model/User'],
    'Model/Totals'   => [ ROOT . '/app/Model/Totals']
]);

/**
 * Custom Inflector rules can be set to correctly pluralize or singularize table, model, controller names or whatever other
 * string is passed to the inflection functions
 *
 * Inflector::rules('singular', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 * Inflector::rules('plural', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 *
 */
/**
 * Plugins need to be loaded manually, you can either load them one by one or all of them in a single call
 * Uncomment one of the lines below, as you need. Make sure you read the documentation on CakePlugin to use more
 * advanced ways of loading plugins
 *
 * CakePlugin::loadAll(); // Loads all plugins at once
 * CakePlugin::load('DebugKit'); //Loads a single plugin named DebugKit
 *
 */
CakePlugin::loadAll(['OAuth' => ['routes' => true]]);

// Dispatch Filters
Configure::write('Dispatcher.filters', [
    'AssetDispatcher',
    'CacheDispatcher'
]);

/**
 * Add NewRelic Component to ensure that any exception can be 
 * tracked properly.
 */
App::uses('NewRelicComponent', 'Controller/Component');

/**
 * Configures default file logging options
 */
App::uses('CakeLog', 'Log');
CakeLog::config('debug', [
    'engine' => 'File',
    'types'  => ['notice', 'info', 'debug'],
    'file'   => 'debug',
]);

// Configure default error handler
App::uses('AppError', 'Lib');
CakeLog::config('error', [
    'engine' => 'File',
    'types'  => ['warning', 'error', 'critical', 'alert', 'emergency'],
    'file'   => 'error',
]);

/**
 * Event Listeners
 */
$env = $_SERVER['server_location'];
//$env = 'live';
if (in_array($env, ['live', 'staging', 'runscope'])){
    App::uses('CakeEventManager', 'Event');
    App::uses('LogListener', 'Event');
    App::uses('AuthenticationListener', 'Event');
    App::uses('GrantListener', 'Event');
    App::uses('RequestCacheListener', 'Event');
    CakeEventManager::instance()->attach(new LogListener());
    CakeEventManager::instance()->attach(new AuthenticationListener());
    CakeEventManager::instance()->attach(new GrantListener());
    //CakeEventManager::instance()->attach(new RequestCacheListener());
}

