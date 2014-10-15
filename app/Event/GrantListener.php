<?php

App::uses('RedisComponent', 'Controller/Component');
App::uses('CakeEventListener', 'Event');
App::uses('Location', 'Model');
App::uses('User', 'Model');
App::uses('UserType', 'Model');

class GrantListener implements CakeEventListener
{

    /**
     * @return array associative array or event key names pointing to the function
     * that should be called in the object when the respective event is fired
     */
    public function implementedEvents ()
    {
        return [
            'Authentication.passed' => [
                'callable' => 'validation',
                'priority' => '1'
            ],
        ];
    }

    public function validation (CakeEvent $event)
    {
        try {
            if (isset($event->data['location_id'])) {
                $this->validateLocationId($event->data['token'], $event->data['location_id']);
            }
        }
        catch (OAuth2AuthenticateException $e) {
            $this->response_code    = $e->getCode();
            $this->response_message = $e->getMessage();
            $e->sendHttpResponse();
            return false;
        }
    }

    private function validateLocationId ($token, $location_id)
    {
        $user    = new User();
        $user->read(null, $token['user_id']);
        $isValid = false;
        switch ($user->data['User']['usertype_id']) {
            case UserType::$SUPER_ADMIN:
            case UserType::$ACCOUNT_MANAGER:
                $isValid = true;
                break;
            case UserType::$RESELLER:
                $isValid = (bool) $user->find(
                    'count', 
                    [
                        'conditions' => [],
                        'fields'     => [
                            'Location.id',
                        ],
                        'joins'      => [
                            [
                                'table'      => 'reseller',
                                'alias'      => 'Reseller',
                                'type'       => 'INNER',
                                'conditions' => [
                                    'User.id = Reseller.user_id',
                                    'User.id' => $user->data['User']['id']
                                ]
                            ],
                            [
                                'table'      => 'location',
                                'alias'      => 'Location',
                                'type'       => 'INNER',
                                'conditions' => [
                                    'Location.reseller_id = Reseller.id',
                                    'Location.id' => $location_id,
                                ]
                            ]
                        ]
                    ]
                );
                break;
            case UserType::$LOCATION_MANAGER:
                $isValid = (bool) $user->find(
                    'count', 
                    [
                        'conditions' => [],
                        'fields'     => [
                            'LocationManagerLocation.location_id',
                        ],
                        'joins'      => [
                            [
                                'table'      => 'locationmanager',
                                'alias'      => 'LocationManager',
                                'type'       => 'INNER',
                                'conditions' => [
                                    'User.id = LocationManager.user_id',
                                    'User.id' => $user->data['User']['id']
                                ]
                            ],
                            [
                                'table'      => 'locationmanager_location',
                                'alias'      => 'LocationManagerLocation',
                                'type'       => 'INNER',
                                'conditions' => [
                                    'LocationManagerLocation.locationmanager_id= LocationManager.id',
                                    'LocationManagerLocation.location_id' => $location_id,
                                ]
                            ]
                        ]
                    ]
                );
                break;
            case UserType::$DEVELOPER:
                $isValid = (bool) $user->find(
                    'count', 
                    [
                        'conditions' => [],
                        'fields'     => [
                            'Location.id',
                        ],
                        'joins'      => [
                            [
                                'table'      => 'developer',
                                'alias'      => 'Developer',
                                'type'       => 'INNER',
                                'conditions' => [
                                    'User.id = Developer.user_id',
                                    'User.id' => $user->data['User']['id']
                                ]
                            ],
                            [
                                'table'      => 'location',
                                'alias'      => 'Location',
                                'type'       => 'INNER',
                                'conditions' => [
                                    'Location.developer_id = Developer.id',
                                    'Location.id' => $location_id,
                                ]
                            ]
                        ]
                    ]
                );
                break;
            case UserType::$EMPLOYEE:
            case UserType::$GUEST:
            default:
                break;
        }
        if (!$isValid) {            
            throw new Exception('You are not allowed to access to this location_id.');
        }
    }

}
