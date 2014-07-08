<?php

App::uses('AppModel', 'Model');

class User extends AppModel {
    public $useDbConfig = 'backstage';
    private $hash_algos = array(
        128 => 'sha512',
        64 => 'sha256',
        40 => 'sha1',
        32 => 'md5'
    );

    public $useTable = 'user';
    public $validate = array(
        'username' => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
                'required' => true,
            ),
            'minLength' => array(
                'rule' => array('minLength', '3'),
            )
        ),
        'email' => array(
            'email'    => 'email', 
            'notEmpty' => array(
                'rule' => array('notEmpty'),
                'required' => true,
            )
        ),
        'firstname' => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
                'required' => true,
            )
        ),
        'lastname' => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
                'required' => true,
            )
        ),
        'password' => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
                'required' => true,
            ),
            'minLength' => array(
                'rule'  => array('minLength', '5')
            ),
        ),
        'confirmPassword' => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
                'required' => true,
            ),
        ),
    );

    public function hash_password($password, $salt = FALSE, $h_byte_size = FALSE) {
        if (!$password OR strlen($password) > 250) {
            return FALSE;
        }
        if ($h_byte_size === FALSE) {
            reset($this->hash_algos);
            $h_byte_size = key($this->hash_algos);
        } elseif (!isset($this->hash_algos[$h_byte_size])) {
            die('Fatal Error: No matching hash algorithm.');
        }
        if ($salt === FALSE) {
            $salt = '';
            for ($i = 0; $i < $h_byte_size; $i++) {
                $salt .= chr(mt_rand(33, 126));
            }
        } elseif (strlen($salt) !== $h_byte_size) {
            $salt = '';
        }
        return array(
            'salt' => $salt,
            'password' => hash($this->hash_algos[$h_byte_size], $salt . $password)
        );
    }

    public function verifyEmail($email) {
        if (empty($email)) return false;
        
        $res = $this->find('first', array(
            'conditions' => array('email' => $email),
            'fields' => array(
                'User.id',
                'User.username',
                'User.usertype_id',
                'User.ts_creation',
                'User.email'
            )
        ));
        return ($res) ? $res['User'] : false;
    }
    
    public function checkEmailExists($email, $userId=0) {
        if (!empty($userId)) {
            $user = $this->find('all', array(
                'conditions' => array(
                    'User.id !=' =>  $userId,
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
    
    public function checkUsernameExists($username, $userId=0) {
        if (!empty($userId)) {
            $user = $this->find('all', array(
                'conditions' => array(
                    'User.id !=' =>  $userId,
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
}