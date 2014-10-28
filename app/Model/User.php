<?php

App::uses('AppModel', 'Model');


/**
 * Class User
 *
 * @property $id integer Unique ID for the User record
 * @property $uuid string Globally unique id for the User
 * @property $email string Email address for the user
 * @property $username string Username
 * @property $password Password
 * @property $salt Salt for encryption of the password
 * @property $usertype_id Access control indicator for the user @see UserType
 * @property $is_demo boolean Flag indicating if the record is only for demo purposes
 * @property $ts_creation string MySQL timestamp when the record was created
 * @property $ts_update string MySQL timestamp when the record was last updated
 */
class User extends AppModel
{
    public $useDbConfig = 'backstage';

	public $actsAs = array('Containable');
    private $hash_algos = array (
        128 => 'sha512',
        64  => 'sha256',
        40  => 'sha1',
        32  => 'md5'
    );
    public $useTable    = 'user';

	public $hasOne = ['Employee','LocationManager'];

	public $hasMany = [
		'UserLocationReport' => [
			'className' => 'UserLocationReport',
			'foreignKey' => 'user_id'
		]
	];

    public $validate    = array (
        'username'        => array (
            'notEmpty'  => array (
                'rule'     => array ('notEmpty'),
                'required' => true,
	            'message' => 'Usernames cannot be empty'
            ),
            'minLength' => array (
                'rule' => array ('minLength', '3'),
	            'message' => 'Usernames must be at least 3 characters long'
            ),
            'checkUsernameExists' => array(
	            'rule' => array('checkUsernameExists'),
	            'message' => 'Username already exists. Please try a different username.',
	            'on' => 'create'
            )
        ),
        'email'           => array (
	        'notEmpty' => array (
		        'rule'     => array ('notEmpty'),
		        'required' => true,
		        'message' => 'Email address cannot be empty'
	        ),
            'email'    => array(
	            'rule' => 'email',
	            'message' => 'Email entered was not valid.',
            ),
	        'checkEmailExists' => array(
		        'rule' => array('checkEmailExists'),
		        'message' => 'Email already exists. Please try a different email.',
		        'on' => 'create'
	        )
        ),
        'firstname'       => array (
            'notEmpty' => array (
                'rule'     => array ('notEmpty'),
                'required' => true,
	            'message' => 'First Name cannot be empty'
            )
        ),
        'lastname'        => array (
            'notEmpty' => array (
                'rule'     => array ('notEmpty'),
                'required' => true,
	            'message' => 'Last Name cannot be empty'
            )
        ),
        'password'        => array (
            'notEmpty'  => array (
                'rule'     => array ('notEmpty'),
                'required' => true,
	            'message' => 'Password cannot be empty'
            ),
            'minLength' => array (
                'rule' => array ('minLength', '5'),
	            'message' => 'Passwords must be at least 5 characters long'

            ),
        ),
        'confirmPassword' => array (
            'notEmpty' => array (
                'rule'     => array ('notEmpty'),
                'required' => true,
	            'message' => 'Confirm Password field cannot be empty',
	            'on' => 'create'
            ),
            'matchesConfirm' => array(
	            'rule' => array('validateConfirmPassword','password'),
	            'message' => 'Password and Confirm Password Field do not match',
	            'on' => 'create'

            )
        ),
    );

    /**
     * Authenticate user
     * 
     * @param string
     * @param string
     * @return array
     */
    public function authenticate ($username, $password)
    {
        $user = $this->find('first', array (
            'conditions' => array ('User.username' => $username),
        ));

        if (empty($user))
            return false;

        $m_salt      = $user['User']['salt'];
        $m_pass      = $user['User']['password'];
        $h_byte_size = strlen($m_pass);

        $hashed_pair = $this->hash_password($password, $m_salt, $h_byte_size);
        if ($hashed_pair === FALSE OR $m_pass !== $hashed_pair['password']) {
            return FALSE;
        }
        unset($user['User']['salt']);
        unset($user['User']['password']);
        return $user['User'];
    }

    public function hash_password ($password, $salt = FALSE, $h_byte_size = FALSE)
    {
        if (!$password OR strlen($password) > 250) {
            return FALSE;
        }
        if ($h_byte_size === FALSE) {
            reset($this->hash_algos);
            $h_byte_size = key($this->hash_algos);
        }
        elseif (!isset($this->hash_algos[$h_byte_size])) {
            die('Fatal Error: No matching hash algorithm.');
        }
        if ($salt === FALSE) {
            $salt = '';
            for ($i = 0; $i < $h_byte_size; $i++) {
                $salt .= chr(mt_rand(33, 126));
            }
        }
        elseif (strlen($salt) !== $h_byte_size) {
            $salt = '';
        }
        return array (
            'salt'     => $salt,
            'password' => hash($this->hash_algos[$h_byte_size], $salt . $password)
        );
    }

    public function verifyEmail ($email)
    {
        if (empty($email))
            return false;

        $res = $this->find('first', array (
            'conditions' => array ('User.email' => $email),
            'fields'     => array (
                'User.id',
                'User.username',
                'User.usertype_id',
                'User.ts_creation',
                'User.email'
            )
        ));
        return ($res) ? $res['User'] : false;
    }


	/**
	 * @param $email
	 * @param integer|array|null $args Either a user id or an array of validation data
	 *
	 * @return bool
	 */
    public function checkEmailExists ($email, $arg2 = null)
    {
	    // If $args is array that means it came from a validation rule and get the id from the object data
	    if(is_array($arg2)){
		    $userId = $this->data[$this->name]['id'];
	    }elseif(is_numeric($arg2)){
		    $userId = $arg2;
	    }else{
		    throw new InvalidArgumentException('Argument 2 should either be an array of validation data or user id');
	    }

        if (!empty($userId)) {
	        $user = $this->find('all', array (
                'conditions' => array (
                    'User.id !=' => $userId,
                    'User.email' => $email
                )
            ));
            if (!empty($user))
                return false;
            else
                return true;
        } else {
            $user = $this->find('first',['conditions' => ['User.email' => $email ]]);
            if (!empty($user))
                return false;
            else
                return true;
        }
    }

    public function checkUsernameExists ($username, $arg2 = 0)
    {
	    // If $args is array that means it came from a validation rule and get the id from the object data
	    if(is_array($arg2)){
		    $userId = $this->data[$this->name]['id'];
	    }elseif(is_numeric($arg2)){
		    $userId = $arg2;
	    }else{
		    throw new InvalidArgumentException('Argument 2 should either be an array of validation data or user id');
	    }
        if (!empty($userId)) {
	        $user = $this->find('all', array (
                'conditions' => array (
                    'User.id !='    => $userId,
                    'User.username' => $username
                )
            ));
            if (!empty($user))
                return false;
            else
                return true;
        } else {
            $user = $this->find('first',['conditions' => ['User.username' => $username ]]);
            if (!empty($user))
                return false;
            else
                return true;
        }
    }

	public function validateConfirmPassword($checkField, $password){

		$fieldName = '';
		foreach ($checkField as $key => $value){
			$fieldName = $key;
			break;
		}
		return $this->data[$this->name][$password] === $this->data[$this->name][$fieldName];
	}

    /**
     * Get an array that contains the list of all the
     * locations associated to the user load into the
     * Model.
     * 
     * @return array
     */
    public function getLocationList ()
    {
        if (empty($this->data) || empty($this->data['User']['usertype_id'])) {
            return [];
        }
        switch ($this->data['User']['usertype_id']) {
            case UserType::$SUPER_ADMIN:
            case UserType::$ACCOUNT_MANAGER:
                $result = $this->find('all', $this->getLocationRoleKeyQuery('accountmanager'));
                break;
            case UserType::$RESELLER:
                $result = $this->find('all', $this->getLocationRoleKeyQuery('reseller'));
                break;
            case UserType::$LOCATION_MANAGER:
                $result = $this->find('all', $this->getLocationManagerLocationsQuery());                
                break;
            case UserType::$DEVELOPER:
                $result = $this->find('all', $this->getLocationRoleKeyQuery('developer'));
                break;
            case UserType::$EMPLOYEE:
                $result = $this->find('all', $this->getEmployeeLocationsQuery());                
                break;
            case UserType::$GUEST:
            default:
                return [];                
        }
        $return = [];
        if(!empty($result)){
            foreach($result as $location){
                $return[] = $location['Location']['location_id'];
            }
        }
        return $return;
    }

    /**
     * Helper function that returns the
     * query that search with locations are related
     * to a role that contains a foreign key into
     * the location's table. 
     * 
     * Example: reseller have a reseller_id column
     * into the location's table.
     * 
     * @param String $role
     * @return array
     */
    private function getLocationRoleKeyQuery ($role)
    {
        $ucRole = ucfirst($role);
        return [
            'conditions' => [],
            'fields'     => ['Location.id as location_id'],
            'joins'      => [
                [
                    'table'      => $role,
                    'alias'      => $ucRole,
                    'type'       => 'INNER',
                    'conditions' => [
                        'User.id = ' . $ucRole . '.user_id',
                        'User.id' => $this->data['User']['id']
                    ]
                ],
                [
                    'table'      => 'location',
                    'alias'      => 'Location',
                    'type'       => 'INNER',
                    'conditions' => ['Location.'.$role.'_id = ' . $ucRole . '.id']
                ]
            ]
        ];
    }

    /**
     * Helper function that returns the 
     * query that needs to be used to recover
     * the locations related to a Location Manager.
     * 
     * @return array
     */
    private function getLocationManagerLocationsQuery ()
    {
        return [
            'conditions' => [],
            'fields'     => [
                'Location.location_id',
            ],
            'joins'      => [
                [
                    'table'      => 'locationmanager',
                    'alias'      => 'LocationManager',
                    'type'       => 'INNER',
                    'conditions' => [
                        'User.id = LocationManager.user_id',
                        'User.id' => $this->data['User']['id']
                    ]
                ],
                [
                    'table'      => 'locationmanager_location',
                    'alias'      => 'Location',
                    'type'       => 'INNER',
                    'conditions' => [
                        'Location.locationmanager_id= LocationManager.id',                        
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Helper function that returns the 
     * query that needs to be used to recover
     * the locations related to an Employee.
     * 
     * @return array
     */
    private function getEmployeeLocationsQuery ()
    {
        return [
            'conditions' => [],
            'fields'     => [
                'Location.location_id',
            ],
            'joins'      => [
                [
                    'table'      => 'employee',
                    'alias'      => 'Employee',
                    'type'       => 'INNER',
                    'conditions' => [
                        'User.id = Employee.user_id',
                        'User.id' => $this->data['User']['id']
                    ]
                ],
                [
                    'table'      => 'location_employee',
                    'alias'      => 'Location',
                    'type'       => 'INNER',
                    'conditions' => [
                        'Location.employee_id= Employee.id',                        
                    ]
                ]
            ]
        ];
    }

}
