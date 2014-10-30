<?php

App::uses('ApiController', 'Controller');
App::uses('ApiComponent', 'Controller/Component');
App::uses('UserType', 'Model');
App::uses('LocationManager', 'Model');
App::uses('Employee', 'Model');
App::uses('LocationEmployee', 'Model');
App::uses('LocationLocationmanager', 'Model');

class UserController extends ApiController
{

    /**
     * Create a new User and Location Manager record from POST
     * @return JsonResponse Returns response object with JSON already set in the body and status code
     */
    public function register ()
    {
        $user               = new User();
        $locationManager    = new LocationManager();
        $newUser            = false;
        $newLocationManager = false;

        $user->create($this->request->data, true);

        if ($user->validates()) {
            // Generate Password Hash
            $password = $user->hash_password($this->request->data('password'));
            $uuid = uniqid();
            $user->set('uuid', $uuid);
            $user->set('salt', $password['salt']);
            $user->set('usertype_id', UserType::$LOCATION_MANAGER);
            $user->set('password', $password['password']);

            // Generate the confirmation password also to check hashing is working correctly
            $confirmPasswordHash = $user->hash_password($this->request->data['confirmPassword'], $password['salt']);
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
                $result = array (
                    'data'    => array (
                        'uuid'               => $uuid,
                        'user_id'            => $user->id,
                        'locationmanager_id' => $locationManager->id
                    ),
                    'options' => array (
                        'endpoint' => '/user/' . __FUNCTION__,
                    ),
                    'message' => array (
                        'success' => 'User has been successfully created.'
                    )
                );

                return new JsonResponse([ 'body' => $result, 'status' => 201]);
            }
            else {
                $user->getDataSource()->rollback();
                $user->validationErrors[] = 'There was an issue persisting the request. Please try again later';
            }
        }

        return new JsonResponse([ 'body' => $user->validationErrors, 'status' => 422]);
    }

    /**
     *
     * @return JsonResponse Returns response object with JSON already set in the body and status code
     */
    public function login ()
    {
        $params = $this->request->data;
        if (empty($params['username']) || empty($params['password'])) {
            return new JsonResponse([
                'body'   => [ 'error' => 'Username and Password are required and cannot be empty'],
                'status' => 401
                    ]);
        }
        $oUser = new User();
        if ($user  = $oUser->authenticate($params['username'], $params['password'])) {
            $results['data']              = array (
                'user_id'     => $user['id'],
                'uuid'        => $user['uuid'],
                'usertype_id' => $user['usertype_id'],
            );
            $results['data']['locations'] = $this->locations($user['uuid'], true);
            $results['options']           = array (
                'endpoint' => '/user/' . __FUNCTION__,
                'username' => $user['username']
            );
            $results['message']           = array (
                'success' => 'User login successful.'
            );
            $status                       = 200;
        }
        else {
            $status  = 401;
            $results = [ 'error' => 'Invalid Credentials Supplied'];
        }

        return new JsonResponse([ 'body' => $results, 'status' => $status]);
    }

    /**
     * Get User info
     *
     * @param Array get data
     *
     * @return Array
     */
    public function getSettings ()
    {

        $uuid = $this->request->query['uuid'];
        if (empty($uuid)) {
            return new JsonResponse([
                'body'   => [ 'error' => 'User not found. Please provide a valid UUID.'],
                'status' => 404
                    ]);
        }
        $userModel  = new User();
        $userBundle = $userModel->find(
                'first', [
            'conditions' => [ 'uuid' => $uuid],
                ]);

        if (empty($userBundle)) {
            return new JsonResponse([
                'body'   => [ 'error' => 'User not found with supplied UUID'],
                'status' => 404
                    ]);
        }
        else {
            $emailPreferences = $userBundle['UserLocationReport'];
            $user             = $userBundle['User'];
        }

        $ret['data'] = array (
            'username' => $user['username'],
            'email'    => $user['email']
        );


        switch ($user['usertype_id']) {
            case UserType::$LOCATION_MANAGER:
                if (!empty($userBundle['LocationManager']) && !empty($userBundle['LocationManager']['id'])) {
                    $ret['data']['firstname'] = $userBundle['LocationManager']['firstname'];
                    $ret['data']['lastname']  = $userBundle['LocationManager']['lastname'];
                }

                $locations = [];
                try {
                    $locations = $this->_getLocations($user['usertype_id'], $user['id']);
                }
                catch (Exception $e) {
                    $this->log($e->getMessage(), 'debug');
                }
                $ret['data']['locations'] = $locations;
                break;
            case UserType::$EMPLOYEE:
                if (!empty($userBundle['Employee']) && !empty($userBundle['Employee']['id'])) {
                    $ret['data']['firstname'] = $userBundle['Employee']['firstname'];
                    $ret['data']['lastname']  = $userBundle['Employee']['lastname'];
                }

                $locations = [];
                try {
                    $locations = $this->_getLocations($user['usertype_id'], $userBundle['Employee']['id']);
                }
                catch (Exception $e) {
                    $this->log($e->getMessage(), 'debug');
                }
                $ret['data']['locations'] = $locations;
                break;
        }
        $ret['data']['uuid']    = $uuid;
        $ret['data']['user_id'] = $user['id'];
        if (!empty($emailPreferences)) {
            foreach ($emailPreferences as $locationPref) {
                $str = '';
                if ($locationPref['daily']) {
                    $str .= 'daily,';
                }
                if ($locationPref['weekly']) {
                    $str .= 'weekly,';
                }
                if ($locationPref['monthly']) {
                    $str .= 'monthly,';
                }
                if (empty($str)) {
                    $str .= 'none';
                }
                else {
                    $str = substr($str, 0, - 1);
                }
                $ret['data']['emailPrefs'][$locationPref['location_id']] = $str;
            }
        }
        $ret['options'] = array (
            'endpoint' => '/user/' . __FUNCTION__,
            'uuid'     => $uuid,
        );

        return new JsonResponse([ 'body' => $ret]);
    }

    /**
     * Update user data
     *
     */
    public function updateSettings ()
    {
        $params = $this->request->data;

        if (empty($params['uuid'])) {
            return new JsonResponse([
                'status' => 404,
                'body'   => [ 'error' => 'User not found. Please provide a valid UUID.'],
                    ]);
        }

        $userModel = new User();

        $user = $userModel->find('first', [
            'recursive'  => 1,
            'conditions' => [
                'uuid' => $params['uuid']
            ]
                ]);

        if (empty($user)) {
            return new JsonResponse([
                'status' => 404,
                'body'   => [ 'error' => 'User not found. Please provide a valid UUID.'],
                    ]);
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

        // Validate the User model first, then validate the associated model if it passes
        $userModel->set($user['User']);
        $userModel->set($params);

        if (!$userModel->validates()) {
            return new JsonResponse([
                'status' => 422,
                'body'   => [
                    'error'  => 'User data does not pass validation',
                    'errors' => $userModel->validationErrors
                ],
                    ]);
        }

        if ($assocUserModelClassname &&
                class_exists($assocUserModelClassname) && !empty($user[$assocUserModelClassname])
        ) {
            /** @var Model $assocUserModel */
            $assocUserModel = new $assocUserModelClassname;
            $assocUserModel->set($user[$assocUserModelClassname]);
            $assocUserModel->set($params);

            if (!$assocUserModel->validates()) {
                return new JsonResponse([
                    'status' => 422,
                    'body'   => [
                        'error'  => 'User data does not pass validation',
                        'errors' => $assocUserModel->validationErrors
                    ],
                        ]);
            }
        }
        else {
            $assocUserModel = false;
        }


        // If we got this far everything has validated so save the data
        $userModel->set('ts_update', date('Y-m-d H:i:s'));
        $userModel->getDataSource()->begin();
        try {
            $userModel->save(null, false, [ 'username', 'email', 'usertype_id', 'ts_update']);

            if ($assocUserModel) {
                $assocUserModel->save(null, false, [ 'firstname', 'lastname']);
            }
            $userModel->getDataSource()->commit();
        }
        catch (Exception $e) {
            $userModel->getDataSource()->rollback();

            return new JsonResponse([
                'status' => 422,
                'body'   => [
                    'error' => 'There was an error processing your request. Please try again or contact support',
                ],
                    ]);
        }

        return new JsonResponse([
            'status' => 202,
            'body'   => [
                array (
                    'message' => array (
                        'success' => 'User data has been successfully saved.'
                    ),
                    'options' => array (
                        'endpoint' => '/user/' . __FUNCTION__,
                        'uuid'     => $params['uuid'],
                    )
                )
            ]
                ]);
    }

    /**
     * Update user password
     *
     * @param Array POST
     *
     * @return Array
     */
    public function updatePassword ()
    {
        $params = $this->request->data;

        $preflightErrors = [];
        if (empty($params['uuid'])) {
            $preflightErrors[] = 'User not found. Please provide a valid UUID.';
        }
        if (empty($params['currentPassword'])) {
            $preflightErrors[] = 'Current password provided does not match with the password in our records.';
        }
        if (empty($params['password']) || empty($params['confirmPassword']) || ( $params['password'] != $params['confirmPassword'] )) {
            $preflightErrors[] = 'Password and confirmPassword do not match.';
        }
        if (strlen($params['password']) < 5) {
            $preflightErrors[] = 'Password must be at least 5 characters long.';
        }

        $userModel           = new User();
        $userModel->set('password', $params['password']);
        $userModel->set('confirmPassword', $params['confirmPassword']);
        $preflightValidation = $userModel->validates([ 'password', 'confirmPassword']);

        if (!empty($preflightErrors) || !$preflightValidation) {
            $errors = array_merge($preflightErrors, $userModel->validationErrors);

            return new JsonResponse([
                'status' => 422,
                'body'   => [
                    'error'  => 'Your request does not pass validation, please try again',
                    'errors' => $errors
                ],
                    ]);
        }
        $userModel->clear();
        $userModel->set('uuid', $params['uuid']);

        if ($userModel->validates([ 'field_list' => 'uuid'])) {
            $userBundle = $userModel->find('first', [
                'recursive'  => - 1,
                'conditions' => [ 'uuid' => $params['uuid']]
                    ]
            );
        }

        if (empty($userBundle)) {
            return new JsonResponse([
                'status' => 404,
                'body'   => [
                    'error' => 'User not found. Please provide a valid UUID.',
                ]
                    ]);
        }
        else {
            $user = $userBundle['User'];
        }

        $current = $userModel->hash_password($params['currentPassword'], $user['salt']);
        if ($user['password'] != $current['password']) {
            return new JsonResponse([
                'status' => 401,
                'body'   => [
                    'error' => 'Current password provided does not match with the password in our records.',
                ],
                    ]);
        }

        $password = $userModel->hash_password($params['password']);
        $userModel->set('password', $password['password']);

        try {
            $userModel->save(null, null, [ 'password']);
        }
        catch (Exception $e) {
            return new JsonResponse([
                'status' => 422,
                'body'   => [
                    'error' => 'There was an error processing your request. Please try again or contact support',
                ],
                    ]);
        }
        $ret = array (
            'message' => array (
                'success' => 'Password updated successfully.'
            ),
            'options' => array (
                'endpoint' => '/user/' . __FUNCTION__,
                'uuid'     => $params['uuid'],
            )
        );

        return new JsonResponse([ 'body' => $ret]);
    }

    /**
     * Get locations associated to a user
     *
     * @param int $uuid The UUID associated witht the user
     *
     * @return JsonResponse
     */
    public function locations ($uuid)
    {
        if (empty($uuid)) {
            return new JsonResponse([
                'status' => 401,
                'body'   => [ 'error' => 'User not found. Please provide a valid UUID.'],
                    ]);
        }

        $userModel = new User();
        $user      = $userModel->find('first', array (
            'recursive'  => - 1,
            'conditions' => array (
                'User.uuid' => $uuid
            )
                ));

        if (empty($user)) {
            return new JsonResponse([
                'status' => 404,
                'body'   => [ 'error' => 'User not found. Please provide a valid UUID.'],
                    ]);
        }
        else {
            $user = $user['User'];
        }

        try {
            $locations = $this->_getLocations($user['usertype_id'], $user['id']);
        }
        catch (InvalidArgumentException $ie) {
            return new JsonResponse([
                'status' => 400,
                'body'   => [ 'error' => $ie->getMessage()],
                    ]);
        }
        catch (Exception $e) {
            return new JsonResponse([
                'status' => 500,
                'body'   => [ 'error' => $e->getMessage()]
                    ]);
        }


        $res['data']['locations'] = $locations;
        $res['options']           = array (
            'endpoint' => '/user/' . __FUNCTION__,
            'uuid'     => $uuid
        );


        return new JsonResponse([ 'body' => $res]);
    }

    /**
     * Internal function to get the locations associated with a user
     *
     * @param $usertype_id
     * @param $user_id
     *
     * @return array of Locations
     * @throws InvalidArgumentException If the usertype is wrong or empty
     *
     */
    protected function _getLocations ($usertype_id, $user_id)
    {

        if (empty($usertype_id) || !in_array($usertype_id, [
                    UserType::$LOCATION_MANAGER,
                    UserType::$EMPLOYEE
                ])
        ) {
            throw new InvalidArgumentException('You need to be a location manager or an employee to have locations associated to you.');
        }

        $locations = [];
        switch ($usertype_id) {
            case UserType::$LOCATION_MANAGER:
                $locationLocationManagerModel = new LocationLocationmanager();
                $locationsBundle              = $locationLocationManagerModel->find(
                        'all', [
                    'conditions' =>
                    [ 'user_id' => $user_id],
                        ]);

                if (!empty($locationsBundle)) {
                    foreach ($locationsBundle as $locationBundle) {
                        if (!empty($locationBundle) && !empty($locationBundle['Location']['id'])) {
                            $locations[$locationBundle['Location']['id']] = $locationBundle['Location']['name'];
                        }
                    }
                }
                break;
            case UserType::$EMPLOYEE:
                $locationEmployeeModel = new LocationEmployee();
                $locationsBundle       = $locationEmployeeModel->find('all', [ 'conditions' => [ 'user_id' => $user_id]]);
                if (!empty($locationsBundle)) {
                    foreach ($locationsBundle as $locationBundle) {
                        if (!empty($locationBundle) && !empty($locationBundle['Location']['id'])) {
                            $locations[$locationBundle['Location']['id']] = $locationBundle['Location']['name'];
                        }
                    }
                }

                return $locations;
                break;
        }

        return $locations;
    }

}
