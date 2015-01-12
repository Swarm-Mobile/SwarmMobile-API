<?php

putenv('server_location=phpunit');

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)));
define('APP', ROOT . DS . 'app' . DS);
define('APP_DIR', ROOT . DS . 'app');
define('WEBROOT_DIR', APP_DIR . DS . 'webroot');
define('WWW_ROOT', dirname(WEBROOT_DIR) . DS);


include dirname(__FILE__) . DS . 'autoload.php';
include dirname(__FILE__) . DS . 'utils' . DS . 'FixtureManager.php';

FixtureManager::healthcheck();
Configure::write('debug', 2);

Debugger::addFormat('console', [
    'error' => "\n\n{:error}: {:code} :: {:description} on line {:line} of {:path}\n{:info}\n",
    'code'  => '',
    'info'  => ''
]);
Debugger::outputAs('console');
