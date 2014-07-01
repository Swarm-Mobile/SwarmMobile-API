<?php

require_once __DIR__ . '/Component/APIComponent.php';
//require_once __DIR__ . '/Component/ConsumerAPIComponent.php';
require_once __DIR__ . '/Component/CompressedFunctions.php';

App::uses('SettingComponent', 'Controller/Component');
App::uses('OAuthComponent', 'OAuth.Controller/Component');
App::uses('AppController', 'Controller');
App::uses('Model', 'Model');
App::uses('RequestModel', 'Model');

App::uses('Location', 'Model');
App::uses('Setting', 'Model');
App::uses('SettingGroup', 'Model');

//ONE for every component that extends from APIComponent
App::uses('PortalComponent', 'Controller/Component');
App::uses('ConsumerComponent', 'Controller/Component');
App::uses('NetworkComponent', 'Controller/Component');
App::uses('OAuthClientComponent', 'Controller/Component');
App::uses('RollupComponent', 'Controller/Component');

App::uses('UserComponent', 'Controller/Component');
App::uses('CouponComponent', 'Controller/Component');
App::uses('CampaignComponent', 'Controller/Component');
App::uses('LocationComponent', 'Controller/Component');

class APIController extends AppController {

    public $default_cache_time = 300;
    public $cache_time_exceptions = array();
    public $uses = array('Inbox');
    public $debug = false;
    public $cache = true;
    public $rollups = true;
    public $user = array('id_user' => 0, 'username' => '');
    public $endpoint = '';
    public $request_start = 0;
    public $request_end = 0;
    public $microtime = 0;
    public $response_code = 200;
    public $response_message = 'OK';
    public $params = array();
    public $helpers = array('Html', 'Session');

    // Controller Actions
    public function logout() {
        $this->Session->destroy('User');
        $this->redirect(Router::url('/login'));
    }

    public function login() {
        if ($this->request->is('post')) {
            $this->Session->destroy('User');
            $redirect = $this->request->data['redirect'];
            if ($this->Auth->login()) {
                if (empty($redirect)) {
                    $res = array();
                    $res['location_id'] = $this->Session->read("Auth.User.location_id");
                    $res['username'] = $this->Session->read("Auth.User.username");
                    $res['uuid'] = $this->Session->read("Auth.User.uuid");
                    echo json_encode($res);
                    //$this->call_log();
                    exit();
                } else {
                    $this->redirect($redirect);
                }
            } else {
                if (empty($redirect)) {
                    $e = new APIException(401, 'authentication_failed', 'Supplied credentials are invalid');
                    $this->response_code = $e->error_no;
                    $this->response_message = $e->error;
                    //$this->call_log();
                    $e->_displayError();
                    return false;
                }
            }
        }
    }

    public function request_client() {
        $data = array(
            'username' => $this->data['username'],
            'redirect_uri' => $this->data['redirect_uri'],
            'description' => $this->data['description']
        );
        $this->Inbox->save($data);
    }

    public function index() {
        $env = getenv('server_location');
        $this->debug = ($env != 'live');
        set_time_limit(3600);
        $this->microtime = microtime(true);
        $this->request_start = date('Y-m-d H:i:s');
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET");
        header("Access-Control-Allow-Headers: X-PINGOTHER");
        header("Access-Control-Max-Age: 1728000");
        header("Content-Type: application/json; charset=UTF-8");
        try {
            if ($this->request->is('get')) {
                $params = $_GET;
                $this->processGET($params);
            } elseif ($this->request->is('post')) {
                $params = $_POST;
                $this->processPOST($params);
            } else {
                throw new APIException(401, 'invalid_grant', "Method Type Requested aren't granted with your access_token");
            }

            $path = func_get_args();
            $this->params = $params;
            if (!isset($path[1])) {
                $path[1] = '';
            }
            $this->endpoint = $path[0] . '/' . $path[1];
            echo json_encode($this->internalCall($path[0], $path[1], $params));
            //$this->call_log();
            exit();
        } catch (OAuth2AuthenticateException $e) {
            $this->response_code = $e->getCode();
            $this->response_message = $e->getMessage();
            //$this->call_log();
            $e->sendHttpResponse();
            return false;
        } catch (APIException $e) {
            $this->response_code = $e->error_no;
            $this->response_message = $e->error;
            //$this->call_log();
            $e->_displayError();
            return false;
        }
    }

    // Internal functions
//    private function call_log() {
//        $this->request_end = date('Y-m-d H:i:s');
//        $oModel = new Model(false, 'calls', 'rollups');
//        $call = array(
//            'id_user' => $this->user['id_user'],
//            'username' => $this->user['username'],
//            'endpoint' => $this->endpoint,
//            'request_start' => $this->request_start,
//            'request_end' => $this->request_end,
//            'response_time' => microtime(true) - $this->microtime,
//            'response_code' => $this->response_code,
//            'response_message' => $this->response_message,
//            'params' => $this->params
//        );
//        $oModel->save($call);
//    }

    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        if (!empty($this->request)) {
            if ($request->is('post')) {
                $this->rollups = false;
                $this->cache = false;
            } elseif ($this->request->is('get')) {
                if (isset($_GET['norollups'])) {
                    $norollups = in_array($_GET['norollups'], ['1', 1, 'yes', true], true);
                    $this->rollups = !$norollups;
                }
                if (isset($_GET['nocache'])) {
                    $nocache = in_array($_GET['nocache'], ['1', 1, 'yes', true], true);
                    $this->cache = !$nocache;
                }
            }
        }
    }

    public function processGET($params = array()) {
        if (!$this->debug) {
            $this->user = $this->authenticate($params['access_token']);
        }
        return true;
    }

    public function processPOST($params = array()) {
        if (!$this->debug) {
            $this->user = $this->authenticate($params['access_token']);
        }
        $this->cache = false;
        $this->rollups = false;
        return true;
    }

    public function internalCall($component, $method, $params) {
        unset($params['access_token']);
        unset($params['norollups']);
        unset($params['nocache']);
        $classname = ucfirst($component) . 'Component';
        if (class_exists($classname)) {
            $oComponent = new $classname($this->request, $this->cache, $this->rollups);
            $result = $this->getPreviousResult($component, $method, $params);
            if ($result === false) {
                $result = $oComponent->$method($params);
                $this->cache($component, $method, $params, $result);
            }
            return $result;
        }
        throw new APIException(404, 'endpoint_not_found', "The requested reference type don't exists");
    }

    private function getPreviousResult($component, $method, $params) {
        unset($params['access_token']);
        unset($params['norollups']);
        unset($params['nocache']);
        unset($params['rollup']);        
        //if ($this->cache) {
        if ($component == 'location' && $method == 'purchaseInfo') {
            $filename = $this->getCacheFilePath($component, $method, $params);
            if (file_exists($filename)) {
                $cache_time = (isset($this->cache_time_exceptions[$component][$method])) ? $this->cache_time_exceptions[$component][$method] : $this->default_cache_time;
                if (time() - filemtime($filename) <= $cache_time) {
                    include $filename;
                    return $result;
                }
            }
        } else if ($this->rollups && $component == 'location' && !in_array($method,['data','totals'])) {
            $oModel = new Model(false, 'walkbys', 'rollups');
            $oDb = $oModel->getDataSource();
            $sSQL = "SELECT * FROM $method WHERE location_id = :location_id AND `date` = :date";
            $aRes = $oModel->fetchAll($sSQL, [':location_id'=>$params['location_id'], ':date'=>$params['start_date']]);
            if(!empty($aRes)){
                $to_return = [
                  'data' => [
                    'totals' => [
                        'open'  => $aRes[0][$method]['total_open'],
                        'close' => $aRes[0][$method]['total_close'],
                        'total' => $aRes[0][$method]['total_total']
                    ],
                    'breakdown' => [
                        $params['start_date'] => [
                            'hours' => [
                                '00'=> $aRes[0][$method]['h00'],
                                '01'=> $aRes[0][$method]['h01'],
                                '02'=> $aRes[0][$method]['h02'],
                                '03'=> $aRes[0][$method]['h03'],
                                '04'=> $aRes[0][$method]['h04'],
                                '05'=> $aRes[0][$method]['h05'],
                                '06'=> $aRes[0][$method]['h06'],
                                '07'=> $aRes[0][$method]['h07'],
                                '08'=> $aRes[0][$method]['h08'],
                                '09'=> $aRes[0][$method]['h09'],
                                '10'=> $aRes[0][$method]['h10'],
                                '11'=> $aRes[0][$method]['h11'],
                                '12'=> $aRes[0][$method]['h12'],
                                '13'=> $aRes[0][$method]['h13'],
                                '14'=> $aRes[0][$method]['h14'],
                                '15'=> $aRes[0][$method]['h15'],
                                '16'=> $aRes[0][$method]['h16'],
                                '17'=> $aRes[0][$method]['h17'],
                                '18'=> $aRes[0][$method]['h18'],
                                '19'=> $aRes[0][$method]['h19'],
                                '20'=> $aRes[0][$method]['h20'],
                                '21'=> $aRes[0][$method]['h21'],
                                '22'=> $aRes[0][$method]['h22'],
                                '23'=> $aRes[0][$method]['h23'],
                            ],
                            'totals' => [                                
                                'close' => $aRes[0][$method]['total_close'],
                                'total' =>  $aRes[0][$method]['total_open'],
                                'open' => $aRes[0][$method]['total_total'],
                            ]
                        ]
                    ],
                  ],
                  'options' => [
                      'endpoint'=>$component.'/'.$method,
                      'location_id'=>$params['location_id'],
                      'start_date'=>$params['start_date'],
                      'end_date'=>$params['end_date']
                  ]
                ];
                return $to_return;
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

    public function authenticate($accessToken = '') {
        $oOAuth = new OAuthComponent(new ComponentCollection());
        $oOAuth->OAuth2->verifyAccessToken($accessToken);
        return $oOAuth->user();
    }

    private function cache($component, $method, $params, $result, $from_rollups = false) {
        if (!empty($result) && ($component . '/' . $method) != 'location/data') {
            unset($params['access_token']);
            unset($params['norollups']);
            unset($params['nocache']);
            unset($params['rollup']);
            if (
                isset($params['start_date']) &&
                isset($params['end_date']) &&
                $params['start_date'] != $params['end_date']
            ) {
                return;
            }            
            //if ($this->cache) {
            if ($component == 'location' && $method == 'purchaseInfo') {
                  $this->createCacheFolders($component, $method);
                  $cache_file = $this->getCacheFilePath($component, $method, $params);
                  $handle = fopen($cache_file, 'w+');
                  fwrite($handle, '<?php $result = ' . var_export($result, true) . ';?>');
                  fclose($handle);
            } else if ($this->rollups) {
                if (!$from_rollups && $component == 'location' && $method != 'data') {
                    $date = $params['start_date'];
                    $location_id = $params['location_id'];
                    $oModel = new Model(false, 'walkbys', 'rollups');
                    $oDb = $oModel->getDataSource();
                    $sSQL = "SELECT id FROM $method WHERE location_id = :location_id AND `date` = :date";
                    $aRes = $oDb->fetchAll($sSQL, [
                        ':location_id'=>$location_id,
                        ':date'=>$date
                    ]);
                    if (empty($aRes)) {
                        $sSQL = <<<SQL
INSERT INTO $method
    SET date = '$date',
        location_id = $location_id,
        total_open = {$result['data']['breakdown'][$date]['totals']['open']},
        total_close = {$result['data']['breakdown'][$date]['totals']['close']},
        total_total = {$result['data']['breakdown'][$date]['totals']['total']},
        h00 = {$result['data']['breakdown'][$date]['hours']['00']['total']},
        h01 = {$result['data']['breakdown'][$date]['hours']['01']['total']},
        h02 = {$result['data']['breakdown'][$date]['hours']['02']['total']},
        h03 = {$result['data']['breakdown'][$date]['hours']['03']['total']},
        h04 = {$result['data']['breakdown'][$date]['hours']['04']['total']},
        h05 = {$result['data']['breakdown'][$date]['hours']['05']['total']},
        h06 = {$result['data']['breakdown'][$date]['hours']['06']['total']},
        h07 = {$result['data']['breakdown'][$date]['hours']['07']['total']},
        h08 = {$result['data']['breakdown'][$date]['hours']['08']['total']},
        h09 = {$result['data']['breakdown'][$date]['hours']['09']['total']},
        h10 = {$result['data']['breakdown'][$date]['hours']['10']['total']},
        h11 = {$result['data']['breakdown'][$date]['hours']['11']['total']},
        h12 = {$result['data']['breakdown'][$date]['hours']['12']['total']},
        h13 = {$result['data']['breakdown'][$date]['hours']['13']['total']},
        h14 = {$result['data']['breakdown'][$date]['hours']['14']['total']},
        h15 = {$result['data']['breakdown'][$date]['hours']['15']['total']},
        h16 = {$result['data']['breakdown'][$date]['hours']['16']['total']},
        h17 = {$result['data']['breakdown'][$date]['hours']['17']['total']},
        h18 = {$result['data']['breakdown'][$date]['hours']['18']['total']},
        h19 = {$result['data']['breakdown'][$date]['hours']['19']['total']},
        h20 = {$result['data']['breakdown'][$date]['hours']['20']['total']},
        h21 = {$result['data']['breakdown'][$date]['hours']['21']['total']},
        h22 = {$result['data']['breakdown'][$date]['hours']['22']['total']},
        h23 = {$result['data']['breakdown'][$date]['hours']['23']['total']},
        ts_creation = NOW(),
        ts_update = NOW()
SQL;
                        $oDb->query($sSQL);
                    } else {
                        //throw new APIException(500, 'duplicated_cache', "This request is already cached");
                    }
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
