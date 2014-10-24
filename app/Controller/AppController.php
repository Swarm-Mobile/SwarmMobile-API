<?php

App::uses('Controller', 'Controller');
App::uses('AuthComponent', 'Controller/Component');
App::uses('ValidatorComponent', 'Controller/Component');

class AppController extends Controller
{

    public $components = array (
        'DebugKit.Toolbar',
        'Session',
        'Auth' => array (
            AuthComponent::ALL => array (
                'userModel'      => 'ExpMember',
                'loginRedirect'  => array (
                    'controller' => 'admin',
                    'action'     => 'home'
                ),
                'logoutRedirect' => array (
                    'controller' => 'pages',
                    'action'     => 'display',
                    'home'
                )
            ),
            'authenticate'     => array (
                'SwarmForm' => array (
                    'userModel'      => 'ExpMember',
                    'loginRedirect'  => array (
                        'controller' => 'admin',
                        'action'     => 'home'
                    ),
                    'logoutRedirect' => array (
                        'controller' => 'pages',
                        'action'     => 'display',
                        'home'
                    )
                )
            ),
            'loginAction'      => array (
                'controller' => 'api',
                'action'     => 'login',
                'plugin'     => null
            )
        )
    );
    public $helpers    = array (
        'Form' => array (
            'className' => 'BootstrapForm'
        )
    );

    public function beforeFilter ()
    {
        $this->Auth->allow('login', 'index', 'display');
    }

    public function validateParams ($params, $rules)
    {
        $return = [];
        foreach ($rules as $k => $validators) {
            if (in_array('required', $validators)) {
                if (!isset($params[$k]))
                    throw new Exception($k . ' is required');
            } elseif (isset($params[$k])) {
                if (is_array($validators)) {
                    foreach ($validators as $validator) {
                        $valid = true;
                        switch ($validator) {
                            case 'positive-int':
                                $valid = ValidatorComponent::isPositiveInt($params[$k]);
                            case 'date':
                                $valid = ValidatorComponent::isDate($params[$k]);
                            case 'device_type':
                                $valid = in_array(strtolower($params[$k]), ['ping', 'portal', 'presence']);
                            default:
                                if (!$valid)
                                    throw new Exception($k . ' must be a valid ' . $validator);
                        }
                        $return[] = $params[$k];
                    }
                } else {
                    $return[] = '';
                }
            }
            else {
                $return[] = '';
            }
            return $return;
        }
    }

}
