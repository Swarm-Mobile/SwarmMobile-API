<?php

App::uses('AppModel', 'Model');

/**
 * ExpMember Model
 * 
 */
class ExpMember extends AppModel {

    var $useDbConfig = 'ee';

    /**
     * Primary key field
     * 
     * @var string
     */
    public $primaryKey = 'member_id';

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'username';
    private $hash_algos = array(
        128 => 'sha512',
        64 => 'sha256',
        40 => 'sha1',
        32 => 'md5'
    );

    /**
     * Authenticate user
     * 
     * @var string
     * @var string
     * @return array
     */
    public function authenticate($username, $password) {
        $res = $this->find('first', array(
            'conditions' => array('username' => $username),
            'fields' => array('ExpMember.member_id', 'ExpMember.group_id', 'ExpMember.username',
                'ExpMember.password', 'ExpMember.salt', 'MemberData.m_field_id_28 as store_id', 'MemberData.m_field_id_128 as uuid'),
            'joins' => array(
                array(
                    'table' => 'exp_member_data',
                    'alias' => 'MemberData',
                    'type' => 'INNER',
                    'conditions' => array(
                        'MemberData.member_id = ExpMember.member_id'
                    )
                )
            )
        ));

        if (empty($res))
            return false;

        $member = array_merge($res['ExpMember'], $res['MemberData']);

        $m_salt = $member['salt'];
        $m_pass = $member['password'];
        $h_byte_size = strlen($m_pass);

        $hashed_pair = $this->hash_password($password, $m_salt, $h_byte_size);
        if ($hashed_pair === FALSE OR $m_pass !== $hashed_pair['password']) {
            return FALSE;
        }
        unset($member['salt']);
        unset($member['password']);
        return $member;
    }

    private function hash_password($password, $salt = FALSE, $h_byte_size = FALSE) {
        // Even for md5, collisions usually happen above 1024 bits, so
        // we artifically limit their password to reasonable size.
        if (!$password OR strlen($password) > 250) {
            return FALSE;
        }

        // No hash function specified? Use the best one
        // we have access to in this environment.
        if ($h_byte_size === FALSE) {
            reset($this->hash_algos);
            $h_byte_size = key($this->hash_algos);
        } elseif (!isset($this->hash_algos[$h_byte_size])) {
            // What are they feeding us? This can happen if
            // they move servers and the new environment is
            // less secure. Nothing we can do but fail. Hard.
            die('Fatal Error: No matching hash algorithm.');
        }

        // No salt? (not even blank), we'll regenerate
        if ($salt === FALSE) {
            $salt = '';

            // The salt should never be displayed, so any
            // visible ascii character is fair game.
            for ($i = 0; $i < $h_byte_size; $i++) {
                $salt .= chr(mt_rand(33, 126));
            }
        } elseif (strlen($salt) !== $h_byte_size) {
            // they passed us a salt that isn't the right length,
            // this can happen if old code resets a new password
            // ignore it
            $salt = '';
        }

        return array(
            'salt' => $salt,
            'password' => hash($this->hash_algos[$h_byte_size], $salt . $password)
        );
    }

}
