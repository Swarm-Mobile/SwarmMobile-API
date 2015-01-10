<?php

App::uses('UserType', 'Model/User');

class UserController extends AppController
{

    protected $locationManager;
    protected $employee;
    protected $location;
    protected $locationEmployee;
    protected $locationLocationManager;
    protected $user;
    protected $userLocationReport;

    public function getLocationManager ()
    {
        if (empty($this->locationManager)) {
            App::uses('LocationManager', 'Model/User');
            $this->locationManager = new LocationManager();
        }
        return $this->locationManager;
    }

    public function getEmployee ()
    {
        if (empty($this->employee)) {
            App::uses('Employee', 'Model/User');
            $this->employee = new Employee();
        }
        return $this->employee;
    }

    public function getLocation ()
    {
        if (empty($this->location)) {
            App::uses('Location', 'Model/Location');
            $this->location = new Location();
        }
        return $this->location;
    }

    public function getLocationEmployee ()
    {
        if (empty($this->locationEmployee)) {
            App::uses('LocationEmployee', 'Model/Location');
            $this->locationEmployee = new LocationEmployee();
        }
        return $this->locationEmployee;
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

    public function getUserLocationReport ()
    {
        if (empty($this->userLocationReport)) {
            App::uses('UserLocationReport', 'Model/User');
            $this->userLocationReport = new UserLocationReport();
        }
        return $this->userLocationReport;
    }

    public function setLocationManager (LocationManager $locationManager)
    {
        $this->locationManager = $locationManager;
        return $this;
    }

    public function setEmployee (Employee $employee)
    {
        $this->employee = $employee;
        return $this;
    }

    public function setLocation (Location $location)
    {
        $this->location = $location;
        return $this;
    }

    public function setLocationEmployee (LocationEmployee $locationEmployee)
    {
        $this->locationEmployee = $locationEmployee;
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
        return $this;
    }

    public function setUserLocationReport (UserLocationReport $userLocationReport)
    {
        $this->userLocationReport = $userLocationReport;
        return $this;
    }

    public function register ()
    {
        $user               = $this->getUser();
        $locationManager    = $this->getLocationManager();
        $newUser            = false;
        $newLocationManager = false;

        $user->create($this->request->data, true);

        if ($user->validates()) {
            // Generate Password Hash
            $password = $user->hash_password($this->request->data('password'));
            $uuid     = uniqid();
            $user->set('uuid', $uuid);
            $user->set('salt', $password['salt']);
            $user->set('usertype_id', UserType::$LOCATION_MANAGER);
            $user->set('password', $password['password']);

            // Generate the confirmation password also to check hashing is working correctly
            $confirmPasswordHash = $user->hash_password($this->request->data('confirmPassword'), $password['salt']);
            $user->set('confirmPassword', $confirmPasswordHash['password']);

            $user->getDataSource()->begin(); // Start a new transaction
            $newUser = $user->save();
            if ($newUser) {
                // Create an entry for LocationManager
                $locationManager->set('user_id', $user->id);
                $locationManager->set('firstname', $this->request->data('firstname'));
                $locationManager->set('lastname', $this->request->data('lastname'));
                $newLocationManager = $locationManager->save();
            }
            if ($newUser && $newLocationManager) {
                $user->getDataSource()->commit();
                $result = [
                    'data'    => [
                        'uuid'               => $uuid,
                        'user_id'            => $user->id,
                        'locationmanager_id' => $locationManager->id
                    ],
                    'options' => ['endpoint' => '/user/' . __FUNCTION__],
                    'message' => ['success' => 'User has been successfully created.']
                ];

                return new JsonResponse([ 'body' => $result, 'status' => 201]);
            }
            else {
                $user->getDataSource()->rollback();
                $user->validationErrors[] = 'There was an issue persisting the request. Please try again later';
            }
        }
        throw new Swarm\UserInputException(SwarmErrorCodes::getFirstError($user->validationErrors));
    }

    public function login ()
    {
        $errors = AppModel::validationErrors(['username', 'password'], $this->request->data, false);
        if (!empty($errors)) {
            throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($errors));
        }

        $params = $this->request->data;
        $oUser  = $this->getUser();
        if ($user   = $oUser->authenticate($params['username'], $params['password'])) {
            $results['data']              = [
                'user_id'     => $user['id'],
                'uuid'        => $user['uuid'],
                'usertype_id' => $user['usertype_id'],
            ];
            $results['data']['locations'] = $this->_getLocations($user['usertype_id'], $user['id']);
            $results['options']           = [
                'endpoint' => '/user/' . __FUNCTION__,
                'username' => $user['username']
            ];
            $results['message']           = ['success' => 'User login successful.'];
        }
        else {
            throw new Swarm\UserInputException(SwarmErrorCodes::INVALID_CREDENTIALS);
        }
        return new JsonResponse([ 'body' => $results]);
    }

    public function getSettings ()
    {
        $errors = AppModel::validationErrors(['uuid'], $this->request->query);
        if (!empty($errors)) {
            throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($errors));
        }

        $uuid      = $this->request->query['uuid'];
        $userModel = $this->getUser();
        $user      = $userModel->find('first', ['conditions' => ['uuid' => $uuid]]);
        if (empty($user)) {
            throw new Swarm\UnprocessableEntityException(SwarmErrorCodes::USER_GETSETTINGS_USER_NOTFOUND);
        }
        $userLocationReport = $this->getUserLocationReport();
        $user               = $user['User'];
        $emailPreferences   = $userLocationReport->find('all', ['conditions' => ['user_id' => $user['id']]]);
        $ret['data']        = [
            'username' => $user['username'],
            'email'    => $user['email']
        ];
        $roles              = [
            UserType::$LOCATION_MANAGER => 'LocationManager',
            UserType::$EMPLOYEE         => 'Employee',
        ];
        if (in_array($user['usertype_id'], array_keys($roles))) {
            $method = 'get' . $roles[$user['usertype_id']];
            $model  = $this->$method();
            $entity = $model->find('first', ['conditions' => ['user_id' => $user['id']]]);
            if (!empty($entity)) {
                $ret['data']['firstname'] = $entity[$roles[$user['usertype_id']]]['firstname'];
                $ret['data']['lastname']  = $entity[$roles[$user['usertype_id']]]['lastname'];
            }
            $ret['data']['locations'] = $this->_getLocations($user['usertype_id'], $user['id']);
        }
        $ret['data']['uuid']    = $uuid;
        $ret['data']['user_id'] = $user['id'];
        if (!empty($emailPreferences)) {
            foreach ($emailPreferences as $locationPref) {
                $str = '';
                foreach (['daily', 'weekly', 'monthly'] as $interval) {
                    if ($locationPref['UserLocationReport'][$interval]) {
                        $str .= $interval . ',';
                    }
                }
                $str = (empty($str)) ? $str . 'none' : substr($str, 0, - 1);
                $ret['data']['emailPrefs'][$locationPref['UserLocationReport']['location_id']] = $str;
            }
        }
        $ret['options'] = [
            'endpoint' => '/user/' . __FUNCTION__,
            'uuid'     => $uuid,
        ];
        return new JsonResponse(['body' => $ret]);
    }

    public function updateSettings ()
    {
        $errors = AppModel::validationErrors(['uuid'], $this->request->data);
        if (!empty($errors)) {
            throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($errors));
        }

        $uuid      = $this->request->data['uuid'];
        $userModel = $this->getUser();
        $user      = $userModel->find('first', ['conditions' => ['uuid' => $uuid]]);
        if (empty($user)) {
            throw new Swarm\UnprocessableEntityException(SwarmErrorCodes::USER_UPDATESETTINGS_USER_NOTFOUND);
        }
        switch ($user['User']['usertype_id']) {
            case UserType::$LOCATION_MANAGER:
                $assocUserModelClassname = 'LocationManager';
                break;
            case UserType::$EMPLOYEE:
                $assocUserModelClassname = 'Employee';
                break;
            default:
                $assocUserModelClassname = false;
        }

        $userModel->set($user['User']);
        $userModel->set($this->request->data);
        $assocUserModel = false;
        if ($assocUserModelClassname) {
            $getAssocUserModelClassname = 'get' . $assocUserModelClassname;
            $assocUserModel             = $this->$getAssocUserModelClassname();
            $assocUserEntity            = $assocUserModel->find('first', ['conditions' => ['user_id' => $user['User']['id']]]);
            $assocUserModel->read(null, $assocUserEntity[$assocUserModelClassname]['id']);
            $assocUserModel->set($this->request->data);
        }
        if (
                !$userModel->validates(['fieldList'=>['username']]) || 
                ($assocUserModel && !$assocUserModel->validates())
            ) {
            $errors = !$userModel->validates() ? $userModel->validationErrors : $assocUserModel->validationErrors;
            throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($errors));
        }
        $userModel->set('ts_update', date('Y-m-d H:i:s'));
        $userModel->save(null, false, ['username', 'email', 'ts_update']);
        if ($assocUserModel) {
            $assocUserModel->save(null, false, ['firstname', 'lastname']);
        }
        return new JsonResponse([
            'status' => 202,
            'body'   => [
                'message' => ['success' => 'User data has been successfully saved.'],
                'options' => [
                    'endpoint' => '/user/' . __FUNCTION__,
                    'uuid'     => $this->request->data['uuid'],
                ]
            ]
        ]);
    }

    public function updatePassword ()
    {
        $errors = AppModel::validationErrors(['uuid', 'currentPassword', 'password', 'confirmPassword'], $this->request->data);
        if (!empty($errors)) {
            throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($errors));
        }

        $params    = $this->request->data;
        $userModel = $this->getUser();
        $user      = $userModel->find('first', ['conditions' => [ 'uuid' => $this->request->data('uuid')]]);
        if (empty($user)) {
            throw new Swarm\UnprocessableEntityException(SwarmErrorCodes::USER_UPDATEPASSWORD_USER_NOTFOUND);
        }
        $userModel->read(null, $user['User']['id']);
        $current = $userModel->hash_password($params['currentPassword'], $user['User']['salt']);
        if ($user['User']['password'] != $current['password']) {
            throw new Swarm\UnprocessableEntityException(SwarmErrorCodes::USER_UPDATEPASSWORD_PASSWORD_MISMATCH);
        }
        $password = $userModel->hash_password($params['password'], $user['User']['salt']);
        $userModel->set('password', $password['password']);
        $userModel->set('salt', $password['salt']);
        $userModel->save(null, null, ['password']);
        $ret      = [
            'message' => ['success' => 'Password updated successfully.'],
            'options' => [
                'endpoint' => '/user/' . __FUNCTION__,
                'uuid'     => $params['uuid'],
            ]
        ];
        return new JsonResponse([ 'body' => $ret]);
    }

    public function locations ()
    {
        $errors = AppModel::validationErrors(['uuid'], $this->request->query);
        if (!empty($errors)) {
            throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($errors));
        }

        $uuid      = $this->request->query['uuid'];
        $userModel = $this->getUser();
        $user      = $userModel->find('first', ['conditions' => ['User.uuid' => $uuid]]);

        if (empty($user)) {
            throw new Swarm\UnprocessableEntityException(SwarmErrorCodes::USER_LOCATIONS_USER_NOTFOUND);
        }

        $res['data']['locations'] = $this->_getLocations($user['User']['usertype_id'], $user['User']['id']);
        $res['options']           = [
            'endpoint' => '/user/' . __FUNCTION__,
            'uuid'     => $uuid
        ];
        return new JsonResponse(['body' => $res]);
    }

    protected function _getLocations ($usertype_id, $user_id)
    {
        $locations = [];
        switch ($usertype_id) {
            case UserType::$LOCATION_MANAGER:
                $locationManagerModel         = $this->getLocationManager();
                $locationManager              = $locationManagerModel->find('first', [
                    'conditions' => ['user_id' => $user_id]
                ]);
                $locationLocationManagerModel = $this->getLocationLocationManager();
                $locations                    = $locationLocationManagerModel->find('all', [
                    'conditions' => ['locationmanager_id' => $locationManager['LocationManager']['id']]
                ]);
                $index                        = 'LocationLocationmanager';
                break;
            case UserType::$EMPLOYEE:
                $employeeModel                = $this->getEmployee();
                $employee                     = $employeeModel->find('first', [
                    'conditions' => ['user_id' => $user_id]
                ]);
                $locationEmployeeModel        = $this->getLocationEmployee();
                $locations                    = $locationEmployeeModel->find('all', [
                    'conditions' => ['employee_id' => $employee['Employee']['id']]
                ]);
                $index                        = 'LocationEmployee';
        }
        $return = [];
        if (!empty($locations)) {
            $locationModel = $this->getLocation();
            foreach ($locations as $location) {
                $location = $locationModel->find('first', ['conditions' => ['id' => $location[$index]['location_id']]]);
                if (!empty($location)) {
                    $return[$location['Location']['id']] = $location['Location']['name'];
                }
            }
        }
        return $return;
    }

}
