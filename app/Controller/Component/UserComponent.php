<?php

App::uses('APIComponent', 'Controller/Component');
App::uses('Model', 'Model');


class UserComponent extends APIComponent {
    /**
     * Register a new user 
     * 
     */
    public function register($params) {
        if (!$this->api->request->is('post')) {
           throw new APIException(400, 'bad_request', 'Incorrect request method.');
        }
        $user = new User();
        $user->set($params);
        $user_id = 0;
        var_dump($_POST);
        if ($user->validates()) {
            if ($params['password'] != $params['confirmPassword']) {
                throw new APIException(400, 'bad_request', 'Passwords do not match.');
            }
            if(!$user->checkEmailExists($params['email'])) {
                throw new APIException(400, 'bad_request', 'Email already exists.');
            }
            
            if(!$user->checkUsernameExists($params['username'])) {
                throw new APIException(400, 'bad_request', 'Username already exists.');
            }
            $oDb  = DBComponent::getInstance('user', 'backstage');
            $password = $user->hash_password($params['password']);
            $uuid = uniqid();
            $sSQL = <<<SQL
INSERT INTO user 
    SET
    username  = :username,
    password  = :password,
    salt = :salt,
    usertype_id = 4,
    email = :email,
    uuid  = :uuid, 
    ts_creation = now()
SQL;
            $oDb->query($sSQL, array(
                ':username'  => $params['username'],
                ':password'  => $password['password'],
                ':salt'      => $password['salt'],
                ':email'     => $params['email'],   
                ':uuid'      => $uuid
            ));
            $user_id = $oDb->lastInsertId();
            // Create an entry for LocationManager
            $sSQL = <<<SQL
INSERT INTO locationmanager
    SET
    user_id   = :user_id,
    firstname = :firstname,
    lastname  = :lastname,
    ts_creation = CURRENT_TIMESTAMP
SQL;
            $oDb->query($sSQL, array(
                ':user_id'   => $user_id,
                ':firstname' => $params['firstname'],
                ':lastname'  => $params['lastname'],
            ));
            $locationmanager_id = $oDb->lastInsertId();
        } else {
            $this->handleValidationErrors($user->validationErrors);
        }
        return array(
            'data' => array(
                'uuid'    => $uuid,
                'user_id' => $user_id,
                'locationmanager_id' => $locationmanager_id
            ),
            'options' => array(
                'endpoint' => '/user/'. __FUNCTION__,
            ),
            'message' => array(
                'success' => 'User has been successfully created.'
            )
        );
    }

    /**
     * Get User info
     * 
     * @param Array get data
     * @return Array
     */
    public function getSettings($params) {
        if(empty($params['uuid'])) {
            throw new APIException(400, 'bad_request', 'A valid UUID needed for getching user settings');
        }
        $user = $this->getUserFromUUID($params['uuid']);
        
        $ret['data']   = array(
            'username' => $user[0]['user']['username'],
            'email'    => $user[0]['user']['email']
        );

        if (!empty($user)) {
            $oDb  = DBComponent::getInstance('user', 'backstage');
            switch($user[0]['user']['usertype_id']) {
                case 4: 
                    $sSQL = <<<SQL
SELECT  firstname, lastname
    FROM locationmanager
    WHERE user_id=:user_id
SQL;
                 $locationmanager = $oDb->fetchAll($sSQL, array(':user_id' => $user[0]['user']['id']));
                 $ret['data']['firstname'] = $locationmanager[0]['locationmanager']['firstname'];
                 $ret['data']['lastname'] = $locationmanager[0]['locationmanager']['lastname'];
                 break;

                case 5:
                    $sSQL = <<<SQL
SELECT  firstname, lastname
    FROM employee
    WHERE user_id=:user_id
SQL;
                 $employee = $oDb->fetchAll($sSQL, array(':user_id' => $params['user_id']));
                 $ret['data']['firstname'] = $employee[0]['locationmanager']['firstname'];
                 $ret['data']['lastname']  = $employee[0]['locationmanager']['lastname'];
                 break;
            }
            $ret['options'] = array(
                'endpoint'  => '/user/'. __FUNCTION__,
                'uuid'      => $params['uuid'],
            );
            return $ret;
        } else {
            throw new APIException(400, 'bad_request', 'A valid UUID is needed to fetch settings.');
        }
    }

    /**
     * Update user data
     * 
     * @param Array post data
     */
    public function updateSettings($params) {
        
        if (!$this->api->request->is('post')) {
           throw new APIException(400, 'bad_request', 'Incorrect request method.');
        }
        if(empty($params['uuid'])) {
            throw new APIException(400, 'bad_request', 'A valid UUID needed for getching user settings');
        }
        $exec = false;
        $user = $this->getUserFromUUID($params['uuid']);
        $oUser = new User();
        if (!empty($user)) {
            $userQ = "UPDATE user SET ";
            $bind = array();
            // Update only if its different than the current email
            if (!empty($params['email']) && $params['email'] != $user[0]['user']['email']) {
                if(!$oUser->checkEmailExists($params['email'], $user[0]['user']['id'])) {
                    throw new APIException(400, 'bad_request', 'Email already exists.');
                }
                $bind[':email'] = $params['email'];
                $exec = true;
                $userQ .= "SET email=:email";
            }
            if (!empty($params['username']) && $params['username'] != $user[0]['user']['username']) {
                if(!$oUser->checkUsernameExists($params['username'], $user[0]['user']['id'])) {
                    throw new APIException(400, 'bad_request', 'Username already exists.');
                }
                $bind[':username'] = $params['username'];
                $exec =true;
                $userQ .= "SET username=:username";
            }
            $oDb  = DBComponent::getInstance('user', 'backstage');
            if ($exec == true) {
                $oDb->query($userQ, $bind);
            }
            $exec = false;
            $bind = array();
            $entityQ = 'UPDATE ';
            switch($user[0]['user']['usertype_id']) {
                case 5:
                    $entityQ .= 'employee SET ';
                    break;
                case 4:
                default:
                    $entityQ  .= 'locationmanager SET '; 
                    
            }
            if (!empty($params['firstname'])) {
                $exec = true;
                $bind[':firstname'] = $params['firstname'];
                $entityQ .= 'firstname=:firstname';
            }
            if (!empty($params['lastname'])) {
                $exec = true;
                $bind[':lastname'] = $params['lastname'];
                $entityQ .= 'lastname=:lastname';
            }
            if ($exec == true) {
                $oDb->query($entityQ, $bind);
            }
            $ret = array(
                'message' => array(
                    'success' => 'User data has been successfully saved.'
                ),
                'options' => array(
                    'endpoint'  => '/user/'. __FUNCTION__,
                    'uuid'      => $params['uuid'], 
                )
            );
        } else {
            throw new APIException(400, 'bad_request', 'User not found. Please provide a valid UUID.');
        }
        return $ret;
    }
}