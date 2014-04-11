<?php

App::uses('OAuthComponent', 'OAuth.Controller/Component');
App::uses('AppController', 'Controller');
App::uses('Model', 'Model');

//ONE for every component that extends from APIComponent
App::uses('ConsumerComponent', 'Controller/Component');
App::uses('MemberComponent', 'Controller/Component');
App::uses('NetworkComponent', 'Controller/Component');
App::uses('StoreComponent', 'Controller/Component');

class APIController extends AppController {

    public $default_cache_time = 300;
    public $cache_time_exceptions = array();
    public $uses = array();
    
    public $debug = true;
    public $cache = false;
    public $rollups = false;
    
    public $user = array('id_user'=>0,'username'=>'');
    public $endpoint = '';
    public $request_start = 0;
    public $request_end = 0;    
    
    public $microtime = 0;
    
    public $response_code = 200;
    public $response_message = 'OK';
    public $params = array();    

    private function call_log() {
        $this->request_end = date('Y-m-d H:i:s');
        $oModel = new Model(false, 'calls', 'mongodb');
        $call = array(
            'id_user'=>$this->user['id_user'],
            'username'=>$this->user['username'],
            'endpoint'=>$this->endpoint,
            'request_start'=>$this->request_start,
            'request_end'=>$this->request_end,
            'response_time'=>  microtime(true) - $this->microtime,
            'response_code'=>$this->response_code,
            'response_message'=>$this->response_message,
            'params'=>$this->params            
        );
        $oModel->save($call);
    }
    
    public function index() {
        set_time_limit(3600);
        $this->microtime = microtime(true);
        $this->request_start = date('Y-m-d H:i:s');
        header("Content-Type: application/json");
        try {
            if ($this->request->is('get')) {
                if (!$this->debug) {
                    $oOAuth = new OAuthComponent(new ComponentCollection());
                    $oOAuth->OAuth2->verifyAccessToken($_GET['access_token']);
                    $this->user = $oOAuth->user();
                }
                $path = func_get_args();
                if (isset($_GET['norollups'])) {
                    $this->rollups = !$_GET['norollups'];
                }
                if (isset($_GET['nocache'])) {
                    $this->cache = !$_GET['nocache'];
                }
                unset($_GET['access_token']);
                unset($_GET['norollups']);
                unset($_GET['nocache']);
                $this->params = $_GET;
                if(!isset($path[1])){
                    $path[1] = '';
                }
                $this->endpoint = $path[0].'/'.$path[1];
                echo json_encode($this->internalCall($path[0], $path[1], $_GET));
                $this->call_log();
                exit();
            }
            throw new APIException(401, 'invalid_grant', "Method Type Requested aren't granted with your access_token");
        } catch (OAuth2AuthenticateException $e) {
            $this->response_code = $e->getCode();
            $this->response_message = $e->getMessage();
            $this->call_log();
            $e->sendHttpResponse();
            return false;
        } catch (APIException $e) {
            $this->response_code = $e->error_no;
            $this->response_message = $e->error;
            $this->call_log();
            $e->_displayError();
            return false;
        }
    }

    public function internalCall($component, $method, $params) {
        $classname = ucfirst($component) . 'Component';
        if (class_exists($classname)) {
            $oComponent = new $classname(new ComponentCollection());
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
        if ($this->rollups) {
            $oModel = new Model(false, 'cache', 'mongodb');
            $conditions = array();
            foreach ($params as $k => $v) {
                $conditions['params.' . $k] = $v;
            }
            $conditions['params.endpoint'] = $component . '/' . $method;

            $aRes = $oModel->find('first', array('conditions' => $conditions, 'order' => array('_id' => -1)));
            if (isset($aRes['Model'])) {
                unset($aRes['Model']['id']);
                unset($aRes['Model']['params']);
                unset($aRes['Model']['modified']);
                unset($aRes['Model']['created']);
                $this->cache($component, $method, $params, $aRes['Model'], true);
                return $aRes['Model'];
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

    private function cache($component, $method, $params, $result, $from_mongo = false) {
        if ($this->cache) {
            $this->createCacheFolders($component, $method);
            $cache_file = $this->getCacheFilePath($component, $method, $params);
            $handle = fopen($cache_file, 'w+');
            fwrite($handle, '<?php $result = ' . var_export($result, true) . ';?>');
            fclose($handle);
            if ($this->rollups) {
                if (!$from_mongo) {
                    $oModel = new Model(false, 'cache', 'mongodb');
                    $result['params'] = array();
                    foreach ($params as $k => $v) {
                        $result['params'][$k] = $v;
                    }
                    $result['params']['endpoint'] = $component . '/' . $method;
                    $oModel->save($result);
                }
            }
        }
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
