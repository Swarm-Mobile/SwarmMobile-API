<?php

App::uses('OAuthComponent', 'OAuth.Controller/Component');
App::uses('AppController', 'Controller');

//ONE for every component that extends from APIComponent
App::uses('ConsumerComponent', 'Controller/Component');
App::uses('MemberComponent', 'Controller/Component');
App::uses('NetworkComponent', 'Controller/Component');
App::uses('StoreComponent', 'Controller/Component');

class APIController extends AppController {

    public $default_cache_time = 30;
    public $cache_time_exceptions = array();
    public $uses = array();
    private $debug = true;
    private $cache = false;

    public function index() {
        header("Content-Type: application/json");
        try {
            if ($this->request->is('get')) {
                if (!$this->debug) {
                    $oOAuth = new OAuthComponent();
                    $oOAuth->OAuth2->verifyAccessToken($_GET['access_token']);
                }
                $path = func_get_args();
                echo json_encode($this->internalCall($path[0], $path[1], $_GET));
                exit();
            }
            throw new APIException(401, 'invalid_grant', "Method Type Requested aren't granted with your access_token");
        } catch (OAuth2AuthenticateException $e) {
            $e->sendHttpResponse();
            return false;
        } catch (APIException $e) {
            $e->_displayError();
            return false;
        }
    }

    public function internalCall($component, $method, $params) {
        $classname = ucfirst($component) . 'Component';
        if (class_exists($classname)) {
            $oComponent = new $classname();
            if (method_exists($oComponent, $method)) {
                $result = $this->getPreviousResult($component, $method, $params);
                if (!$result) {
                    $result = $oComponent->$method($params);
                    $this->cache($component, $method, $params, $result);
                }
                return $result;
            }
            throw new APIException(404, 'endpoint_not_found', "The requested reference method don't exists");
        }
        throw new APIException(404, 'endpoint_not_found', "The requested reference type don't exists");
    }

    private function getPreviousResult($component, $method, $params) {
        if (!$this->cache) {
            return false;
        }
        $filename = $this->getCacheFilePath($component, $method, $params);
        if (file_exists($filename)) {
            $cache_time = (isset($this->cache_time_exceptions[$component][$method])) ? $this->cache_time_exceptions[$component][$method] : $this->default_cache_time;
            if (time() - filemtime($filename) <= $cache_time) {
                include $filename;
                return $result;
            }
        }
        return false;
    }

    private function getCacheFilePath($component, $method, $params) {
        $component = strtolower($component);
        $method = strtolower($method);
        $path = ROOT . DS . 'app' . DS . 'tmp' . DS . 'cache' . DS . 'api_calls' . DS . $component . DS . $method;
        $tmp = '';
        foreach ($params as $k => $v) {
            $tmp .= $k . ':' . $v;
        }
        return $path . DS . md5($tmp) . '.cache';
    }

    private function createCacheFolders($component, $method) {
        $component = strtolower($component);
        $method = strtolower($method);
        $path = ROOT . DS . 'app' . DS . 'tmp' . DS . 'cache' . DS . 'api_calls';
        if (!file_exists($path)) {
            mkdir($path);
        }

        $path = $path . DS . $component;
        if (!file_exists($path)) {
            mkdir($path);
        }

        $path .= DS . $method;
        if (!file_exists($path)) {
            mkdir($path);
        }
    }

    private function cache($component, $method, $params, $result) {
        $this->createCacheFolders($component, $method);
        $cache_file = $this->getCacheFilePath($component, $method, $params);
        $handle = fopen($cache_file, 'w+');
        fwrite($handle, '<?php $result = ' . var_export($result, true) . ';?>');
        fclose($handle);
    }

}

class APIException extends Exception {

    public $error_no;
    public $error;
    public $description;
    
    public function __construct($error_no, $error, $description) {       
        parent::__construct($error_no);
        $this->error_no = $error_no;
        $this->error = $error;
        $this->description = $description;
    }

    public function _displayError() {
        header("Cache-Control: no-store");
        header("HTTP/1.1 {$this->error_no}");
        echo json_encode(
                array(
                    'error' => $this->error, 
                    'error_description' => $this->description
                )
            );
        die();
    }

}
