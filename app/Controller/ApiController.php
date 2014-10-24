<?php

require_once __DIR__ . '/Component/APIComponent.php';
require_once __DIR__ . '/Component/CompressedFunctions.php';

App::uses('EmailQueueComponent', 'Controller/Component');
App::uses('SettingComponent', 'Controller/Component');
App::uses('OAuthComponent', 'OAuth.Controller/Component');
App::uses('AppController', 'Controller');
App::uses('Model', 'Model');
App::uses('RequestModel', 'Model');
App::uses('User', 'Model');
App::uses('Location', 'Model');
App::uses('Setting', 'Model');
App::uses('SettingGroup', 'Model');

//ONE for every component that extends from APIComponent
App::uses('PortalComponent', 'Controller/Component');
App::uses('NetworkComponent', 'Controller/Component');

App::uses('UserComponent', 'Controller/Component');
App::uses('CouponComponent', 'Controller/Component');
App::uses('CampaignComponent', 'Controller/Component');
App::uses('LocationComponent', 'Controller/Component');

/**
 * Class APIController
 */
class APIController extends AppController
{

	/**
	 * @var int Defines a fallback for the file cache expiration if the component doesnt use one
	 */
	public $default_cache_time    = 300;
	/**
	 * @var array
	 */
	public $cache_time_exceptions = array ();
	/**
	 * @var array Defines what pieces of data are available for caching through the internal call
	 */
	public $cache_methods         = [
        'avgTicket',
        'presenceConversionRate',
        'devices',
        'dwell',
        'presenceTraffic',
        'itemsPerTransaction',
        'returning',
        'revenue',
        'timeInShop',
        'totalItems',
        'totals',
        'traffic',
        'transactions',
        'walkbys',
        'windowConversion',
    ];
	/**
	 * @var bool
	 */
	public $iterative             = true;
	/**
	 * @var bool
	 */
	public $debug                 = false;
	/**
	 * @var bool
	 */
	public $cache                 = true;
	/**
	 * @var bool
	 */
	public $rollups               = true;
	/**
	 * @var array
	 */
	public $user                  = array ('id_user' => 0, 'username' => '');
	/**
	 * @var string
	 */
	public $endpoint              = '';
	/**
	 * @var array
	 */
	public $params                = array ();
	/**
	 * @var array
	 */
	public $helpers               = array ('Html', 'Session');

	/**
	 * @var array A list of http methods that are available in the system. If the method type sent in is not in the list
	 *          we wont even try to check in the method access list
	 */
	public $availableHttpMethods = [
		'get',
		'post',
		'put',
		'delete'
	];

	/**
	 * @var array Access list for each controller action with the key being the method and the value being an array of
	 *          available http methods. If there is no key matching the route will use the default. If there is no
	 *          method defined defaults to deny access
	 */
	public $methodAccessList          = [
		'default' => [
			'get' => true,
			'post' => true,
			'put' => false,
			'delete' => false
		]
	];

	/**
	 * @return array
	 * @throws InvalidArgumentException If not http methods are available
	 */
	public function getAvailableHttpMethods() {
		$availableMethods = $this->availableHttpMethods;

		if(empty($availableMethods)){
			throw new InvalidArgumentException('No acceptable http methods defined');
		}

		return $availableMethods;
	}

	/**
	 * @param array $availableHttpMethods
	 *
	 * @return $this
	 */
	public function setAvailableHttpMethods( $availableHttpMethods ) {
		$this->availableHttpMethods = $availableHttpMethods;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isCache() {
		return $this->cache;
	}

	/**
	 * @param boolean $cache
	 *
	 * @return $this
	 */
	public function setCache( $cache ) {
		$this->cache = $cache;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getCacheMethods() {
		return $this->cache_methods;
	}

	/**
	 * @param array $cache_methods
	 *
	 * @return $this
	 */
	public function setCacheMethods( $cache_methods ) {
		$this->cache_methods = $cache_methods;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getCacheTimeExceptions() {
		return $this->cache_time_exceptions;
	}

	/**
	 * @param array $cache_time_exceptions
	 *
	 * @return $this
	 */
	public function setCacheTimeExceptions( $cache_time_exceptions ) {
		$this->cache_time_exceptions = $cache_time_exceptions;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isDebug() {
		return $this->debug;
	}

	/**
	 * @param boolean $debug
	 *
	 * @return $this
	 */
	public function setDebug( $debug ) {
		$this->debug = $debug;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getDefaultCacheTime() {
		return $this->default_cache_time;
	}

	/**
	 * @param int $default_cache_time
	 *
	 * @return $this
	 */
	public function setDefaultCacheTime( $default_cache_time ) {
		$this->default_cache_time = $default_cache_time;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getEndpoint() {
		return $this->endpoint;
	}

	/**
	 * @param string $endpoint
	 *
	 * @return $this
	 */
	public function setEndpoint( $endpoint ) {
		$this->endpoint = $endpoint;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getHelpers() {
		return $this->helpers;
	}

	/**
	 * @param array $helpers
	 *
	 * @return $this
	 */
	public function setHelpers( $helpers ) {
		$this->helpers = $helpers;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isIterative() {
		return $this->iterative;
	}

	/**
	 * @param boolean $iterative
	 *
	 * @return $this
	 */
	public function setIterative( $iterative ) {
		$this->iterative = $iterative;

		return $this;
	}

	/**
	 * @param string $action The action to return a specific list for
	 * @return array
	 */
	public function getMethodAccessList($action = null) {
		if($action === NULL){
			return $this->methodAccessList;
		}else{
			if(array_key_exists($action, $this->methodAccessList)){
				return $this->methodAccessList[$action];
			}else{
				if(!array_key_exists('default',$this->methodAccessList)){
					throw new InvalidArgumentException('Default Method Access list is not defined');
				}else{
					return $this->methodAccessList['default'];
				}
			}
		}
	}

	/**
	 * @param array $methodAccessList
	 *
	 * @return $this
	 */
	public function setMethodAccessList( $methodAccessList ) {
		if(!is_array($methodAccessList)){
			throw new InvalidArgumentException('Method Access List must be an array');
		}

		if(!array_key_exists('default',$methodAccessList)){
			throw new InvalidArgumentException('Method Access List must contain a default / fallback list');
		}

		$this->methodAccessList = $methodAccessList;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getParams() {
		return $this->params;
	}

	/**
	 * @param array $params
	 *
	 * @return $this
	 */
	public function setParams( $params ) {
		$this->params = $params;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isRollups() {
		return $this->rollups;
	}

	/**
	 * @param boolean $rollups
	 *
	 * @return $this
	 */
	public function setRollups( $rollups ) {
		$this->rollups = $rollups;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * @param array $user
	 *
	 * @return $this
	 */
	public function setUser( $user ) {
		$this->user = $user;

		return $this;
	}


	/**
	 * Called before the controller action. Used to filter method types against access list
	 */
	public function beforeFilter(){
		$requestMethod = $this->request->method();
		if(empty($requestMethod)){
			throw new InvalidArgumentException('No request method available');
		}

		$requestMethod = strtolower($requestMethod);
		if(!in_array($requestMethod,$this->getAvailableHttpMethods())){
			$strMethodsAllowed = strtoupper(implode(', ',$this->getAvailableHttpMethods()));
			return new CakeResponse([
				'body' => 'We only allow '.$strMethodsAllowed.' on this version of the API',
				'code' => 405,
				'type' => 'json'
			]);
		}
		$action = $this->request->params['action'];

		if(empty($action)){
			return new CakeResponse([
				'body' => 'Could not match action',
				'code' => 404,
				'type' => 'json'
			]);
		}

		$methodAccessList = $this->getMethodAccessList($action);

		if(!in_array($requestMethod,$methodAccessList) || $methodAccessList[$requestMethod] !== true){
			return new CakeResponse([
				'body' => strtoupper($requestMethod) . ' not available for this endpoint',
				'code' => 405,
				'type' => 'json'
			]);
		}

		parent::beforeFilter();
	}

    // Controller Actions
	/**
	 * @throws Exception
	 */
	public function index ()
    {                
        $env                 = getenv('server_location');
        $this->debug         = ($env != 'live');

        if ($this->request->is('get')) {
            $params = $this->request->query;
        }
        elseif ($this->request->is('post')) {
            $params = $this->request->data;
        }
        else {
            throw new Exception("Method Type Requested aren't granted with your access_token", 401);
        }
        $path         = explode('/', $this->request->url);
        $this->params = $params;
        foreach ([0, 1] as $k) {
            if (!isset($path[$k])) {
                $path[$k] = '';
            }
        }        
        $this->endpoint = $path[0] . '/' . $path[1];
        $component      = ucfirst($path[0]) . 'Component';
        if (class_exists($component) && !empty($path[0])) {
            $component = new $component;
        }
        else {
            throw new Exception("The requested reference type don't exists", 401);
        }
        $result = $this->internalCall($path[0], $path[1], $params);                
        $this->set('result', $result);
        $this->render('/API/json');
    }

	/**
	 * @param $component
	 * @param $method
	 * @param $params
	 *
	 * @return array|bool
	 * @throws Exception
	 */
	public function internalCall ($component, $method, $params)
    {
        unset($params['access_token']);
        unset($params['norollups']);
        unset($params['nocache']);
        $classname = ucfirst($component) . 'Component';
        if (class_exists($classname)) {
            $oComponent = new $classname($this->request, $this->cache, $this->rollups);
            $result     = $this->getPreviousResult($component, $method, $params);
            if ($result === false) {
                $result = $oComponent->$method($params);
                $this->cache($component, $method, $params, $result);
            }            
            return $result;        
        }
        throw new Exception("The requested reference type don't exists", 401);
    }

	/**
	 * @param $component
	 * @param $method
	 * @param $params
	 *
	 * @return array|bool
	 * @throws Exception
	 */
	private function getPreviousResult ($component, $method, $params)
    {
        unset($params['access_token']);
        unset($params['norollups']);
        unset($params['nocache']);
        unset($params['rollup']);
        if ($component == 'location' && $method == 'purchaseInfo') {
            $filename = $this->getCacheFilePath($component, $method, $params);
            if (file_exists($filename)) {
                $cache_time = (isset($this->cache_time_exceptions[$component][$method])) ? $this->cache_time_exceptions[$component][$method] : $this->default_cache_time;
                if (time() - filemtime($filename) <= $cache_time) {
                    include $filename;
                    return $result;
                }
            }
        }
        else if ($this->rollups && $component == 'location' && in_array($method, $this->cache_methods)) {
            $oModel = new Model(false, 'walkbys', 'rollups');
            $oDb    = $oModel->getDataSource();
            $sSQL   = <<<SQL
SELECT * 
FROM $method  
WHERE location_id = :location_id  
  AND `date` = :start_date
  AND `date` = :end_date
SQL;
            $aRes   = $oDb->fetchAll($sSQL, [
                ':location_id' => $params['location_id'],
                ':start_date'  => $params['start_date'],
                ':end_date'    => $params['end_date']
                    ]
            );
            if (!empty($aRes)) {
                if ($method != 'totals') {
                    $data      = $this->internalCall('location', 'data', array ('location_id' => $params['location_id']));
                    $weekday   = new DateTime($params['start_date']);
                    $weekday   = strtolower(date_format($weekday, 'l'));
                    $tmp       = $data['data'][$weekday . '_open'];
                    $isOpen    = $tmp != 0;
                    $open      = ($isOpen) ? (int) strstr($tmp, ':', true) : -1;
                    $tmp       = $data['data'][$weekday . '_close'];
                    $close     = ($isOpen) ? (int) strstr($tmp, ':', true) : -1;
                    $to_return = [
                        'data'    => [
                            'totals'    => [
                                'open'  => $aRes[0][$method]['total_open'],
                                'close' => $aRes[0][$method]['total_close'],
                                'total' => $aRes[0][$method]['total_total']
                            ],
                            'breakdown' => [
                                $params['start_date'] => [
                                    'hours'  => [
                                        '00' => ['open' => ($isOpen ? (0 >= $open && 0 <= $close) : false), 'total' => $aRes[0][$method]['h00']],
                                        '01' => ['open' => ($isOpen ? (1 >= $open && 1 <= $close) : false), 'total' => $aRes[0][$method]['h01']],
                                        '02' => ['open' => ($isOpen ? (2 >= $open && 2 <= $close) : false), 'total' => $aRes[0][$method]['h02']],
                                        '03' => ['open' => ($isOpen ? (3 >= $open && 3 <= $close) : false), 'total' => $aRes[0][$method]['h03']],
                                        '04' => ['open' => ($isOpen ? (4 >= $open && 4 <= $close) : false), 'total' => $aRes[0][$method]['h04']],
                                        '05' => ['open' => ($isOpen ? (5 >= $open && 5 <= $close) : false), 'total' => $aRes[0][$method]['h05']],
                                        '06' => ['open' => ($isOpen ? (6 >= $open && 6 <= $close) : false), 'total' => $aRes[0][$method]['h06']],
                                        '07' => ['open' => ($isOpen ? (7 >= $open && 7 <= $close) : false), 'total' => $aRes[0][$method]['h07']],
                                        '08' => ['open' => ($isOpen ? (8 >= $open && 8 <= $close) : false), 'total' => $aRes[0][$method]['h08']],
                                        '09' => ['open' => ($isOpen ? (9 >= $open && 9 <= $close) : false), 'total' => $aRes[0][$method]['h09']],
                                        '10' => ['open' => ($isOpen ? (10 >= $open && 10 <= $close) : false), 'total' => $aRes[0][$method]['h10']],
                                        '11' => ['open' => ($isOpen ? (11 >= $open && 11 <= $close) : false), 'total' => $aRes[0][$method]['h11']],
                                        '12' => ['open' => ($isOpen ? (12 >= $open && 12 <= $close) : false), 'total' => $aRes[0][$method]['h12']],
                                        '13' => ['open' => ($isOpen ? (13 >= $open && 13 <= $close) : false), 'total' => $aRes[0][$method]['h13']],
                                        '14' => ['open' => ($isOpen ? (14 >= $open && 14 <= $close) : false), 'total' => $aRes[0][$method]['h14']],
                                        '15' => ['open' => ($isOpen ? (15 >= $open && 15 <= $close) : false), 'total' => $aRes[0][$method]['h15']],
                                        '16' => ['open' => ($isOpen ? (16 >= $open && 16 <= $close) : false), 'total' => $aRes[0][$method]['h16']],
                                        '17' => ['open' => ($isOpen ? (17 >= $open && 17 <= $close) : false), 'total' => $aRes[0][$method]['h17']],
                                        '18' => ['open' => ($isOpen ? (18 >= $open && 18 <= $close) : false), 'total' => $aRes[0][$method]['h18']],
                                        '19' => ['open' => ($isOpen ? (19 >= $open && 19 <= $close) : false), 'total' => $aRes[0][$method]['h19']],
                                        '20' => ['open' => ($isOpen ? (20 >= $open && 20 <= $close) : false), 'total' => $aRes[0][$method]['h20']],
                                        '21' => ['open' => ($isOpen ? (21 >= $open && 21 <= $close) : false), 'total' => $aRes[0][$method]['h21']],
                                        '22' => ['open' => ($isOpen ? (22 >= $open && 22 <= $close) : false), 'total' => $aRes[0][$method]['h22']],
                                        '23' => ['open' => ($isOpen ? (23 >= $open && 23 <= $close) : false), 'total' => $aRes[0][$method]['h23']],
                                    ],
                                    'totals' => [
                                        'isOpen' => $isOpen,
                                        'close'  => $aRes[0][$method]['total_close'],
                                        'total'  => $aRes[0][$method]['total_total'],
                                        'open'   => $aRes[0][$method]['total_open'],
                                    ]
                                ]
                            ],
                        ],
                        'options' => [
                            'endpoint'    => '/' . $component . '/' . $method,
                            'location_id' => $params['location_id'],
                            'start_date'  => $params['start_date'],
                            'end_date'    => $params['end_date']
                        ]
                    ];
                    return APIComponent::nightClubFormat($to_return, $data);
                }
                else {
                    return [
                        'walkbys'                => $aRes[0][$method]['walkbys'],
                        'footTraffic'            => $aRes[0][$method]['footTraffic'],
                        'presenceTraffic'        => $aRes[0][$method]['presenceTraffic'],
                        'portalTraffic'          => $aRes[0][$method]['portalTraffic'],
                        'transactions'           => $aRes[0][$method]['transactions'],
                        'revenue'                => $aRes[0][$method]['revenue'],
                        'totalItems'             => $aRes[0][$method]['totalItems'],
                        'returning'              => $aRes[0][$method]['returning'],
                        'timeInShop'             => $aRes[0][$method]['timeInShop'],
                        'traffic'                => $aRes[0][$method]['traffic'],
                        'devices'                => $aRes[0][$method]['devices'],
                        'itemsPerTransaction'    => $aRes[0][$method]['itemsPerTransaction'],
                        'windowConversion'       => $aRes[0][$method]['windowConversion'],
                        'avgTicket'              => $aRes[0][$method]['avgTicket'],
                        'conversionRate'         => $aRes[0][$method]['conversionRate'],
                        'portalConversionRate'   => $aRes[0][$method]['portalConversionRate'],
                        'presenceConversionRate' => $aRes[0][$method]['presenceConversionRate'],
                        'dwell'                  => $aRes[0][$method]['dwell'],
                    ];
                }
            }
        }
        return false;
    }

	/**
	 * @param $component
	 * @param $method
	 * @param $params
	 *
	 * @return string
	 */
	private function getCacheFilePath ($component, $method, $params)
    {
        $component = strtolower($component);
        $method    = strtolower($method);
        $path      = ROOT . DS . 'app' . DS . 'tmp' . DS . 'cache' . DS . 'api_calls' . DS . $component . DS . $method;
        $tmp       = '';
        foreach ($params as $k => $v) {
            $tmp .= $k . ':' . $v;
        }
        return $path . DS . md5($tmp) . '.cache';
    }

	/**
	 * @param $component
	 * @param $method
	 */
	private function createCacheFolders ($component, $method)
    {
        $component = strtolower($component);
        $method    = strtolower($method);
        $path      = ROOT . DS . 'app' . DS . 'tmp' . DS . 'cache' . DS . 'api_calls';
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

	/**
	 * @param $component
	 * @param $method
	 * @param $params
	 * @param $result
	 * @param bool $from_rollups
	 */
	private function cache ($component, $method, $params, $result, $from_rollups = false)
    {
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

            if ($component == 'location' && $method == 'purchaseInfo') {
                $this->createCacheFolders($component, $method);
                $cache_file = $this->getCacheFilePath($component, $method, $params);
                $handle     = fopen($cache_file, 'w+');
                fwrite($handle, '<?php $result = ' . var_export($result, true) . ';?>');
                fclose($handle);
            }
            else if ($this->rollups) {
                if (!$from_rollups && $component == 'location' && in_array($method, $this->cache_methods)) {
                    $date        = $params['start_date'];
                    $location_id = $params['location_id'];
                    $oModel      = new Model(false, 'walkbys', 'rollups');
                    $oDb         = $oModel->getDataSource();
                    $sSQL        = "SELECT id FROM $method WHERE location_id = :location_id AND `date` = :date";
                    $aRes        = $oDb->fetchAll($sSQL, [
                        ':location_id' => $location_id,
                        ':date'        => $date
                    ]);
                    if (empty($aRes)) {
                        if ($method != 'totals') {
                            $sSQL = "
INSERT IGNORE INTO $method
    SET date = '$date',
        location_id = $location_id,
        total_open = " . coalesce($result['data']['breakdown'][$date]['totals']['open'], 0) . ",
        total_close = " . coalesce($result['data']['breakdown'][$date]['totals']['close'], 0) . ",
        total_total = " . coalesce($result['data']['breakdown'][$date]['totals']['total'], 0) . ",
        h00 = " . coalesce($result['data']['breakdown'][$date]['hours']['00']['total'], 0) . ",
        h01 = " . coalesce($result['data']['breakdown'][$date]['hours']['01']['total'], 0) . ",
        h02 = " . coalesce($result['data']['breakdown'][$date]['hours']['02']['total'], 0) . ",
        h03 = " . coalesce($result['data']['breakdown'][$date]['hours']['03']['total'], 0) . ",
        h04 = " . coalesce($result['data']['breakdown'][$date]['hours']['04']['total'], 0) . ",
        h05 = " . coalesce($result['data']['breakdown'][$date]['hours']['05']['total'], 0) . ",
        h06 = " . coalesce($result['data']['breakdown'][$date]['hours']['06']['total'], 0) . ",
        h07 = " . coalesce($result['data']['breakdown'][$date]['hours']['07']['total'], 0) . ",
        h08 = " . coalesce($result['data']['breakdown'][$date]['hours']['08']['total'], 0) . ",
        h09 = " . coalesce($result['data']['breakdown'][$date]['hours']['09']['total'], 0) . ",
        h10 = " . coalesce($result['data']['breakdown'][$date]['hours']['10']['total'], 0) . ",
        h11 = " . coalesce($result['data']['breakdown'][$date]['hours']['11']['total'], 0) . ",
        h12 = " . coalesce($result['data']['breakdown'][$date]['hours']['12']['total'], 0) . ",
        h13 = " . coalesce($result['data']['breakdown'][$date]['hours']['13']['total'], 0) . ",
        h14 = " . coalesce($result['data']['breakdown'][$date]['hours']['14']['total'], 0) . ",
        h15 = " . coalesce($result['data']['breakdown'][$date]['hours']['15']['total'], 0) . ",
        h16 = " . coalesce($result['data']['breakdown'][$date]['hours']['16']['total'], 0) . ",
        h17 = " . coalesce($result['data']['breakdown'][$date]['hours']['17']['total'], 0) . ",
        h18 = " . coalesce($result['data']['breakdown'][$date]['hours']['18']['total'], 0) . ",
        h19 = " . coalesce($result['data']['breakdown'][$date]['hours']['19']['total'], 0) . ",
        h20 = " . coalesce($result['data']['breakdown'][$date]['hours']['20']['total'], 0) . ",
        h21 = " . coalesce($result['data']['breakdown'][$date]['hours']['21']['total'], 0) . ",
        h22 = " . coalesce($result['data']['breakdown'][$date]['hours']['22']['total'], 0) . ",
        h23 = " . coalesce($result['data']['breakdown'][$date]['hours']['23']['total'], 0) . ",
        ts_creation = NOW(),
        ts_update = NOW()
";
                        }
                        else {
                            $sSQL = <<<SQL
INSERT IGNORE INTO $method
    SET location_id             = {$params['location_id']},
        date                    = '{$params['start_date']}',
        walkbys                 = {$result['walkbys']},        
        footTraffic             = {$result['footTraffic']},
        presenceTraffic         = {$result['presenceTraffic']},
        portalTraffic           = {$result['portalTraffic']},
        transactions            = {$result['transactions']},
        revenue                 = {$result['revenue']},
        totalItems              = {$result['totalItems']},
        returning               = {$result['returning']},
        timeInShop              = {$result['timeInShop']},
        traffic                 = {$result['traffic']},
        devices                 = {$result['devices']},
        itemsPerTransaction     = {$result['itemsPerTransaction']},
        windowConversion        = {$result['windowConversion']},
        avgTicket               = {$result['avgTicket']},
        conversionRate          = {$result['conversionRate']},
        presenceConversionRate  = {$result['presenceConversionRate']},
        portalConversionRate    = {$result['portalConversionRate']},
        dwell                   = {$result['dwell']},
        ts_creation             = NOW(),        
        ts_update               = NOW()        
SQL;
                        }
                        $oDb->query($sSQL);
                    }
                }
            }
        }
    }

}
