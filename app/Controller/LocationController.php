<?php

App::uses('Model', 'Model');
App::uses('UserType', 'Model/User');
App::uses('LocationSetting', 'Model/Location');
App::uses('EmailQueueComponent', 'Controller/Component');

class LocationController extends AppController
{

    protected $invoice;
    protected $locationSetting;
    protected $location;
    protected $setting;
    protected $employee;
    protected $developer;
    protected $reseller;
    protected $accountManager;
    protected $locationEmployee;
    protected $locationManager;
    protected $locationLocationManager;
    protected $user;

    public function getInvoice ()
    {
        if (empty($this->invoice)) {
            App::uses('Invoice', 'Model/POS');
            $this->invoice = new Invoice();
        }
        return $this->invoice;
    }

    public function getLocationSetting ()
    {
        if (empty($this->locationSetting)) {
            App::uses('LocationSetting', 'Model/Location');
            $this->locationSetting = new LocationSetting();
        }
        return $this->locationSetting;
    }

    public function getLocation ()
    {
        if (empty($this->location)) {
            App::uses('Location', 'Model/Location');
            $this->location = new Location();
        }
        return $this->location;
    }

    public function getSetting ()
    {
        if (empty($this->setting)) {
            App::uses('Setting', 'Model/Location');
            $this->setting = new Setting();
        }
        return $this->setting;
    }

    public function getAccountManager ()
    {
        if (empty($this->accountManager)) {
            App::uses('AccountManager', 'Model/User');
            $this->accountManager = new AccountManager();
        }
        return $this->accountManager;
    }

    public function getEmployee ()
    {
        if (empty($this->employee)) {
            App::uses('Employee', 'Model/User');
            $this->employee = new Employee();
        }
        return $this->employee;
    }

    public function getDeveloper ()
    {
        if (empty($this->developer)) {
            App::uses('Developer', 'Model/User');
            $this->developer = new Developer();
        }
        return $this->developer;
    }

    public function getReseller ()
    {
        if (empty($this->reseller)) {
            App::uses('Reseller', 'Model/User');
            $this->reseller = new Reseller();
        }
        return $this->reseller;
    }

    public function getLocationEmployee ()
    {
        if (empty($this->locationEmployee)) {
            App::uses('LocationEmployee', 'Model/Location');
            $this->locationEmployee = new LocationEmployee();
        }
        return $this->locationEmployee;
    }

    public function getLocationManager ()
    {
        if (empty($this->locationManager)) {
            App::uses('LocationManager', 'Model/User');
            $this->locationManager = new LocationManager();
        }
        return $this->locationManager;
    }

    public function getLocationLocationManager ()
    {
        if (empty($this->locationLocationManager)) {
            App::uses('LocationLocationmanager', 'Model/Location');
            $this->locationLocationManager = new LocationLocationmanager();
        }
        return $this->locationLocationManager;
    }

    public function getUser ()
    {
        if (empty($this->user)) {
            App::uses('User', 'Model/User');
            $this->user = new User();
        }
        return $this->user;
    }

    public function setInvoice (Invoice $invoice)
    {
        $this->invoice = $invoice;
        return $this;
    }

    public function setLocationSetting (LocationSetting $locationSetting)
    {
        $this->locationSetting = $locationSetting;
        return $this;
    }

    public function setLocation (Location $location)
    {
        $this->location = $location;
        return $this;
    }

    public function setSetting (Setting $setting)
    {
        $this->setting = $setting;
        return $this;
    }

    public function setAccountManager (AccountManager $accountManager)
    {
        $this->accountManager = $accountManager;
        return $this;
    }

    public function setEmployee (Employee $employee)
    {
        $this->employee = $employee;
        return $this;
    }

    public function setDeveloper (Developer $developer)
    {
        $this->developer = $developer;
        return $this;
    }

    public function setReseller (Reseller $reseller)
    {
        $this->reseller = $reseller;
        return $this;
    }

    public function setLocationEmployee (LocationEmployee $locationEmployee)
    {
        $this->locationEmployee = $locationEmployee;
        return $this;
    }

    public function setLocationManager (LocationManager $locationManager)
    {
        $this->locationManager = $locationManager;
        return $this;
    }

    public function setLocationLocationManager (LocationLocationmanager $locationLocationManager)
    {
        $this->locationLocationManager = $locationLocationManager;
        return $this;
    }

    public function setUser (User $user)
    {
        $this->user = $user;
        return $user;
    }

    public function highlights ()
    {
        $fieldsToValidate = ['location_id'];
        $fieldsToValidate = array_merge($fieldsToValidate, !empty($this->request->query('start_date')) ? ['start_date'] : []);
        $fieldsToValidate = array_merge($fieldsToValidate, !empty($this->request->query('start_date')) ? ['end_date'] : []);
        $errors           = AppModel::validationErrors($fieldsToValidate, $this->request->query);
        if (!empty($errors)) {
            throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($errors));
        }

        $location         = $this->getLocation();
        $location->read(null, $this->params->query['location_id']);
        $locationSetting  = $this->getLocationSetting();
        $locationSetting->create(['location_id' => $location->id], true);
        $storeId          = $locationSetting->getSettingValue(LocationSetting::POS_STORE_ID);
        $locationTimezone = $locationSetting->getSettingValue(LocationSetting::TIMEZONE);
        if (empty($storeId)) {
            throw new Swarm\UnprocessableEntityException(SwarmErrorCodes::LOCATION_HIGHLIGHTS_STORE_NOTFOUND);
        }
        $invoice = $this->getInvoice();
        $p       = $this->params->query;
        $result  = array_filter([
            'Biggest Ticket' => $invoice->biggestTicket($storeId, $p['start_date'], $p['end_date'], $locationTimezone),
            'Best Hour'      => $invoice->bestHour($storeId, $p['start_date'], $p['end_date'], $locationTimezone),
            'Best Day'       => $invoice->bestDay($storeId, $p['start_date'], $p['end_date'], $locationTimezone)
        ]);
        return new JsonResponse(['body' => $result]);
    }

    public function getSettings ()
    {
        $errors = AppModel::validationErrors(['location_id'], $this->request->query);
        if (!empty($errors)) {
            throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($errors));
        }

        $result       = [];
        $locationId   = $this->request->query['location_id'];
        $settingModel = $this->getSetting();
        $location     = $this->getLocation();
        $location->read(null, $locationId);
        if (empty($location->data['Location'])) {
            throw new Swarm\UnprocessableEntityException(SwarmErrorCodes::LOCATION_GETSETTINGS_LOCATION_NOTFOUND);
        }
        $result['data']['name']    = $location->data['Location']['name'];
        $settings                  = $settingModel->getSettingsByLocation($locationId);
        $result                    = $this->_getSettingsProcessSettings($result, $settings);
        $result                    = $this->_getSettingsProcessDefaults($result, $settingModel->getDefaults());
        $result['data']['devices'] = $location->getDevices();
        $result['options']         = [
            'endpoint'    => '/location/' . __FUNCTION__,
            'location_id' => $locationId
        ];
        return new JsonResponse(['body' => $result]);
    }

    public function updateSettings ()
    {
        $errors = AppModel::validationErrors(['location_id', 'uuid'], $this->request->data);
        if (!empty($errors)) {
            throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($errors));
        }

        $location = $this->getLocation();
        $data     = $this->request->data;
        $this->_locationFieldValidation($location, $data);

        $locationId = $this->request->data('location_id');
        $location->read(null, $locationId);
        if (empty($location->data['Location'])) {
            throw new Swarm\UnprocessableEntityException(SwarmErrorCodes::LOCATION_UPDATESETTINGS_LOCATION_NOTFOUND);
        }
        if (!empty($data['Location'][$location->id])) {
            if (!empty($data['Location']['name'])) {
                $location->save(['Location' => ['name' => $data['Location']['name']]], false, ['name']);
            }
            foreach ($data['Location'][$location->id] as $key => $val) {
                if (defined('LocationSetting::' . strtoupper($key))) {
                    $settingId = constant('LocationSetting::' . strtoupper($key));
                    $this->_saveLocationSetting($location, $settingId, $key, $val);
                }
            }
        }
        return new JsonResponse(['body' => [
                'options' => [
                    'endpoint'    => '/location/updateSettings',
                    'uuid'        => $data['uuid'],
                    'location_id' => $location->id
                ],
                'message' => [
                    'success' => empty($location->data['Location']) 
                        ? 'Settings have been successfully saved.' 
                        : 'Nothing to update.'
                ]
        ]]);
    }

    public function create ()
    {
        $errors = AppModel::validationErrors(['uuid', 'name'], $this->request->data);
        if (!empty($errors)) {
            throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($errors));
        }

        $data                     = $this->request->data;
        $data['Location']['name'] = $data['name'];
        $location                 = $this->getLocation();
        $location->create($data, false);
        if ($location->validates() && $this->_locationFieldValidation($location, $data)) {
            $userModel = $this->getUser();
            $user      = $userModel->find('first', ['conditions' => ['uuid' => $data['uuid']]]);
            if (empty($user)) {
                throw new Swarm\UnprocessableEntityException(SwarmErrorCodes::LOCATION_CREATE_USER_NOTFOUND);
            }
            $userModel->read(null, $user['User']['id']);
            $returnData['user_id'] = $userModel->data['User']['id'];
            $returnData            = $this->_createLocation($location, $data, $userModel, $returnData);
            $this->_createLocationRoleRelationships($location, $userModel);
            $this->_createLocationSettings($location, $data);
            $this->_sendLocationCreateEmail($location, $data);
            $result                = [
                'data'    => $returnData,
                'options' => [
                    'endpoint' => '/location/' . __FUNCTION__,
                    'uuid'     => $data['uuid']
                ],
                'message' => ['success' => 'Location has been successfully created.']
            ];
            return new JsonResponse(['body' => $result]);
        }
        throw new Swarm\UnprocessableEntityException(SwarmErrorCodes::getFirstError($location->validationErrors));
    }

    public function availableSettings ()
    {
        $errors = AppModel::validationErrors(['uuid'], $this->request->query);
        if (!empty($errors)) {
            throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($errors));
        }

        $settingModel = $this->getSetting();
        $settings     = $settingModel->find('all');
        $result       = [];
        foreach ($settings as $setting) {
            $result['data']['settings'][$setting['Setting']['name']] = [
                'id'      => $setting['Setting']['id'],
                'label'   => $setting['Setting']['label'],
                'desc'    => $setting['Setting']['desc'],
                'default' => $setting['Setting']['default']
            ];
        }
        return new JsonResponse(['body' => $result]);
    }

    public function openHours ()
    {
        $errors = AppModel::validationErrors(['location_id'], $this->request->query);
        if (!empty($errors)) {
            throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($errors));
        }

        $locationId = $this->request->query['location_id'];
        $location   = $this->getLocation();
        $location->read(null, $locationId);
        if (empty($location->data['Location'])) {
            throw new Swarm\UnprocessableEntityException(SwarmErrorCodes::LOCATION_OPENHOURS_LOCATION_ID_EMPTY);
        }
        $ls     = $this->getLocationSetting();
        $ls->setLocationId($locationId);
        $openHours = $ls->getOpenHours();
        $result = ['data'=>[]];
        $days   = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        foreach($days as $day){
            $result['data'][$day]['open'] = $openHours[$day]['open'];
            $result['data'][$day]['close'] = $openHours[$day]['close'];
        }        
        return new JsonResponse(['body' => $result]);
    }

    private function _getSettingsProcessSettings ($result, $settings)
    {
        if (!empty($settings)) {
            foreach ($settings as $setting) {
                $result['data']['settings'][$setting['Setting']['name']] = [
                    'label'       => $setting['Setting']['label'],
                    'setting_id'  => $setting['LocationSetting']['setting_id'],
                    'value'       => $setting['LocationSetting']['value'],
                    'description' => $setting['Setting']['desc']
                ];
            }
        }
        return $result;
    }

    private function _getSettingsProcessDefaults ($result, $defaults)
    {
        foreach ($defaults as $setting) {
            if (
                    empty($result['data']['settings'][$setting['Setting']['name']]) ||
                    empty($result['data']['settings'][$setting['Setting']['name']]['value'])
            ) {
                $result['data']['settings'][$setting['Setting']['name']] = [
                    'label'       => $setting['Setting']['label'],
                    'setting_id'  => $setting['Setting']['id'],
                    'value'       => $setting['Setting']['default'],
                    'description' => $setting['Setting']['desc']
                ];
            }
        }
        return $result;
    }

    private function _sendLocationCreateEmail ($location, $data)
    {
        //TODO: Move to New Relic
        $subject = "Location #" . $location->id . ' ( ' . $data['name'] . ' ) was added from API';
        $msg     = <<<TEXT
<div>
    Location {$location->id} was just added/modified on the
    MDM using the API with the following info:
</div>
<ul>
    <li>Location Name: {$data['name']}</li>    
    <li>Address      : {$data['address1']}</li>    
    <li>City         : {$data['city']}</li>    
    <li>Country      : {$data['country']}</li>
    <li>ZipCode      : {$data['zipcode']}</li>
</ul>
TEXT;
        EmailQueueComponent::queueEmail('info@swarm-mobile.com', 'Info', 'am@swarm-mobile.com', 'AM', $subject, $msg);
    }

    private function _createLocation ($location, $data, $user, $returnData)
    {
        $now          = date('Y-m-d H:i:s');
        $locationData = [
            'Location' => [
                'accountmanager_id' => null,
                'reseller_id'       => null,
                'developer_id'      => null,
                'lead_id'           => null,
                'name'              => $data['name'],
                'description'       => '',
                'ts_creation'       => $now,
                'ts_update'         => $now,
                'cda_imported'      => null
            ]
        ];
        switch ($user->data['User']['usertype_id']) {
            case UserType::$ACCOUNT_MANAGER:
            case UserType::$SUPER_ADMIN:
                $accountManagerModel                           = $this->getAccountManager();
                $accountManager                                = $accountManagerModel->find('first', [
                    'conditions' => ['user_id' => $user->data['User']['id']]
                ]);
                $locationData['Location']['accountmanager_id'] = $accountManager['AccountManager']['id'];
                $returnData['accountmanager_id']               = $accountManager['AccountManager']['id'];
                break;

            case UserType::$DEVELOPER:
                $developerModel                           = $this->getDeveloper();
                $developer                                = $developerModel->find('first', [
                    'conditions' => ['user_id' => $user->data['User']['id']]
                ]);
                $locationData['Location']['developer_id'] = $developer['Developer']['id'];
                $returnData['developer_id']               = $developer['Developer']['id'];
                break;

            case UserType::$RESELLER:
                $resellerModel                           = $this->getReseller();
                $reseller                                = $resellerModel->find('first', [
                    'conditions' => ['user_id' => $user->data['User']['id']]
                ]);
                $locationData['Location']['reseller_id'] = $reseller['Reseller']['id'];
                $returnData['reseller_id']               = $reseller['Reseller']['id'];
                break;
            case UserType::$EMPLOYEE:
                $employeeModel                           = $this->getEmployee();
                $employee                                = $employeeModel->find('first', [
                    'conditions' => ['user_id' => $user->data['User']['id']]
                ]);
                $returnData['employee_id']               = $employee['Employee']['id'];
            case UserType::$LOCATION_MANAGER:
                $locationManagerModel                    = $this->getLocationManager();
                $locationManager                         = $locationManagerModel->find('first', [
                    'conditions' => ['user_id' => $user->data['User']['id']]
                ]);
                $returnData['locationmanager_id']        = $locationManager['LocationManager']['id'];
            default:
            //Do nothing                    
        }
        $location->save($locationData, true, array_keys($locationData['Location']));
        $returnData['location_id'] = $location->id;
        return $returnData;
    }

    private function _createLocationRoleRelationships ($location, $user)
    {
        switch ($user->data['User']['usertype_id']) {
            case UserType::$EMPLOYEE:
                $employeeModel    = $this->getEmployee();
                $employee         = $employeeModel->find('first', [
                    'conditions' => ['user_id' => $user->data['User']['id']]
                ]);
                $locationEmployee = $this->getLocationEmployee();
                $locationEmployee->save(['LocationEmployee' => [
                        'employee_id' => $employee['Employee']['id'],
                        'location_id' => $location->id
                    ]], true, ['employee_id', 'location_id']);

            case UserType::$LOCATION_MANAGER:
                $locationManagerModel    = $this->getLocationManager();
                $locationManager         = $locationManagerModel->find('first', [
                    'conditions' => ['user_id' => $user->data['User']['id']]
                ]);
                $locationLocationManager = $this->getLocationLocationManager();
                $locationLocationManager->save(['LocationLocationmanager' => [
                        'locationmanager_id' => $locationManager['LocationManager']['id'],
                        'location_id'        => $location->id
                    ]], true, ['locationmanager_id', 'location_id']);

            case UserType::$ACCOUNT_MANAGER:
            case UserType::$SUPER_ADMIN:
            case UserType::$DEVELOPER:
            case UserType::$RESELLER:
            default:
            //Do Nothing
        }
    }

    private function _createLocationSettings ($location, $data)
    {        
        foreach (['address1', 'address2', 'city', 'state', 'country', 'zipcode'] as $key) {
            if (array_key_exists($key, $data['Location'])) {
                $this->locationSetting = null;
                $locationSetting       = $this->getLocationSetting();
                $locationSetting->save(
                    [
                        'Location' => [
                            'location_id' => $location->id,
                            'setting_id'  => constant('LocationSetting::' . strtoupper($key)),
                            'value'       => $data['Location'][$key]
                        ]
                   ], true, ['location_id', 'setting_id', 'value']
                );
            }
        }
    }

    private function _locationFieldValidation ($location, $data)
    {
        if (isset($data['address1']) && isset($data['city'])) {
            $locationId = (isset($data['location_id'])) ? $data['location_id'] : 0;
            if ($location->nameAddressCombination($data, isset($data['name']), $locationId) > 0) {
                throw new Swarm\UnprocessableEntityException(SwarmErrorCodes::DUPLICATE_NAME_ADDRESS_COMBINATION);
            }
        }
        if (isset($data['country'])) {
            $data['country'] = strtoupper($data['country']);
            if (!$location->countryCodeExists($data['country'])) {
                throw new Swarm\UnprocessableEntityException(SwarmErrorCodes::COUNTRY_NOT_FOUND);
            }
        }
        return true;
    }

    private function _saveLocationSetting ($location, $settingId, $key, $val)
    {
        $locationSettingModel = $this->getLocationSetting();
        $locationSetting      = $locationSettingModel->find('first', [
            'conditions' => [
                'location_id' => $location->id,
                'setting_id'  => $settingId
            ]
        ]);
        $locationSettingModel->read(null, $locationSetting['LocationSetting']['id']);
        $locationSettingModel->save(['LocationSetting' => [
                'location_id' => $location->id,
                'setting_id'  => $settingId,
                'value'       => $val
        ]]);
    }

}
