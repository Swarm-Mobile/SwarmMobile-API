<?php

App::uses('AppModel', 'Model');

class User extends AppModel
{

    public $useDbConfig = 'backstage';
    private $hash_algos = array (
        128 => 'sha512',
        64  => 'sha256',
        40  => 'sha1',
        32  => 'md5'
    );
    public $useTable    = 'user';
    public $validate    = array (
        'username'        => array (
            'notEmpty'  => array (
                'rule'     => array ('notEmpty'),
                'required' => true,
            ),
            'minLength' => array (
                'rule' => array ('minLength', '3'),
            )
        ),
        'email'           => array (
            'email'    => 'email',
            'notEmpty' => array (
                'rule'     => array ('notEmpty'),
                'required' => true,
            )
        ),
        'firstname'       => array (
            'notEmpty' => array (
                'rule'     => array ('notEmpty'),
                'required' => true,
            )
        ),
        'lastname'        => array (
            'notEmpty' => array (
                'rule'     => array ('notEmpty'),
                'required' => true,
            )
        ),
        'password'        => array (
            'notEmpty'  => array (
                'rule'     => array ('notEmpty'),
                'required' => true,
            ),
            'minLength' => array (
                'rule' => array ('minLength', '5')
            ),
        ),
        'confirmPassword' => array (
            'notEmpty' => array (
                'rule'     => array ('notEmpty'),
                'required' => true,
            ),
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

    public function checkEmailExists ($email, $userId = 0)
    {
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
            $user = $this->findByEmail($email);
            if (!empty($user))
                return false;
            else
                return true;
        }
    }

    public function checkUsernameExists ($username, $userId = 0)
    {
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
            $user = $this->findByUsername($username);
            if (!empty($user))
                return false;
            else
                return true;
        }
    }

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
                $result = $user->find('all', $this->getLocationRoleKeyQuery('reseller'));
                break;
            case UserType::$LOCATION_MANAGER:
                $result = $user->find('all', $this->getLocationManagerLocationsQuery());                
                break;
            case UserType::$DEVELOPER:
                $result = $user->find('all', $this->getLocationRoleKeyQuery('developer'));
                break;
            case UserType::$EMPLOYEE:
                $result = $user->find('all', $this->getEmployeeLocationsQuery());                
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
                    'conditions' => ['Location.developer_id = ' . $ucRole . '.id']
                ]
            ]
        ];
    }

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
