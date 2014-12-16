<?php

App::uses('Model', 'Model');
App::uses('ValidatorComponent', 'Controller/Component');

class AppModel extends Model
{

    public $useDbConfig = 'oauth';
    public $useTable = null;
    public $validate    = [];

    public static function validationErrors ($fields = [], $source = [], $checkSpecialCases = true)
    {        
        $class = get_called_class();
        $validationModel = new $class();
        if($class == 'AppModel'){
            $validationModel->useTable = false;
        }
        $validationModel->setValidationFields($fields, $checkSpecialCases);
        $validationModel->create($source);            
        return $validationModel->validates() ? [] : $validationModel->validationErrors;
    }

    public function setValidationFields ($fields = [], $checkSpecialCases = true)
    {
        $this->validate = [];
        foreach ($fields as $field) {
            switch ($field) {
                case 'type':                    
                    $this->validate[$field] = array_merge(
                        $this->_getNotEmptyValidationRule($field),
                        $this->_getDeviceTypeValidationRule($field)
                    );
                    break;
                case 'start_date':
                case 'end_date':
                    $this->validate[$field] = array_merge(
                        $this->_getNotEmptyValidationRule($field),
                        $this->_getDateValidationRule($field)
                    );
                    break;
                case 'month':
                    $this->validate[$field] = array_merge(
                        $this->_getNotEmptyValidationRule($field),
                        $this->_getMonthValidationRule($field)
                    );
                    break;
                case 'year':
                    $this->validate[$field] = array_merge(
                        $this->_getNotEmptyValidationRule($field),
                        $this->_getYearValidationRule($field)
                    );
                    break;
                case 'password':
                    $this->validate[$field] = array_merge(
                        $this->_getNotEmptyValidationRule($field),
                        $this->_getMinLenghtValidationRule($field, 5)
                    );
                    break;
                case 'confirmPassword':
                    $this->validate[$field] = array_merge(
                        $this->_getNotEmptyValidationRule($field),
                        $this->_getMatchesValidationRule($field, 'password')
                    );
                    break;
                case 'username':
                    $this->validate[$field] = array_merge(
                        $this->_getNotEmptyValidationRule($field),
                        $this->_getMinLenghtValidationRule($field, 5),
                        ($checkSpecialCases)?$this->_getUsernameExistsValidationRule($field):[]                        
                    );
                    break;
                case 'email':
                    $this->validate[$field] = array_merge(
                        $this->_getNotEmptyValidationRule($field),
                        $this->_getEmailValidationRule($field),
                        ($checkSpecialCases)?$this->_getEmailExistsValidationRule($field):[]                        
                    );
                    break;
                case 'devicetype_id':
                case 'deviceenvironment_id':
                case 'location_id':
                case 'customer_id':
                case 'user_id':
                case 'major':
                case 'minor':
                    $this->validate[$field] = array_merge(
                        $this->_getNotEmptyValidationRule($field),
                        $this->_getIntValidationRule($field)
                    );
                    break;
                case 'serial':
                case 'manufacturer_serial':                
                    $this->validate[$field] = array_merge(
                        $this->_getNotEmptyValidationRule($field),
                        ($checkSpecialCases)?$this->_getUniqueValidationRule($field):[]
                    );
                    break;
                case 'currentPassword':
                case 'mac':
                case 'alias':
                case 'name':
                case 'description':
                case 'uuid':
                case 'source':
                case 'address1':
                case 'city':
                case 'zipcode':
                case 'country':
                case 'firstname':
                case 'lastname':
                case 'company':
                case 'state':
                case 'country':
                case 'phone':
                case 'fullname':
                case 'ts':
                case 'firmware_version':
                    $this->validate[$field] = array_merge(
                        $this->_getNotEmptyValidationRule($field)
                    );
                    break;
                default:                                        
                    throw new Swarm\ApplicationErrorException(SwarmErrorCodes::VALIDATOR_NOT_FOUND);
            }
        }        
        return $this;
    }

    private function _getNotEmptyValidationRule ($field)
    {
        return [
            'notEmpty' => [
                'rule'     => ['notEmpty'],
                'required' => true,
                'message'  => $field . ' cannot be empty'
            ]
        ];
    }

    private function _getIntValidationRule ($field)
    {
        return [
            'int' => [
                'rule'    => '/^[0-9]+$/',
                'message' => $field . ' must be an integer'
            ]
        ];
    }

    private function _getUniqueValidationRule ($field)
    {
        return [
            'isUnique' => [
                'rule'     => ['isUnique'],
                'required' => true,                
                'message'  => $field . ' already exists'              
            ]
        ];
    }

    private function _getMonthValidationRule ($field)
    {
        return [
            'isMonth' => [
                'rule'    => ['isMonth'],
                'message' => $field . ' must be between 01 and 12 (0 included in months < 10).'
            ]
        ];
    }

    private function _getYearValidationRule ($field)
    {
        return [
            'isMonth' => [
                'rule'    => ['isYear'],
                'message' => $field . ' must be a valid year after 2012.'
            ]
        ];
    }

    private function _getMinLenghtValidationRule ($field, $length)
    {
        return [
            'minLength' => [
                'rule'    => ['minLength', $length],
                'message' => $field . ' must be at least ' . $length . ' characters long'
            ],
        ];
    }

    private function _getMatchesValidationRule ($field, $matchesField)
    {
        return [
            'matchesConfirm' => [
                'rule'    => ['validateConfirmPassword', $matchesField],
                'message' => $field . ' and ' . $matchesField . ' do not match',
                'on'      => 'create'
            ]
        ];
    }

    private function _getUsernameExistsValidationRule ($field)
    {
        return [
            'checkUsernameExists' => [
                'rule'    => ['checkUsernameExists'],
                'message' => $field . ' already exists. Please try a different one.',
                'on'      => 'create'
            ]
        ];
    }

    private function _getEmailValidationRule ($field)
    {
        return [
            'email' => [
                'rule'    => 'email',
                'message' => $field . ' entered was not valid.',
            ]
        ];
    }

    private function _getEmailExistsValidationRule ($field)
    {
        return [
            'checkEmailExists' => [
                'rule'    => ['checkEmailExists'],
                'message' => $field . ' already exists. Please try a different one.',
                'on'      => 'create'
            ]
        ];
    }

    private function _getDateValidationRule ($field)
    {
        return [
            'date' => [
                'rule'    => ['date'],
                'message' => $field . ' must be a date (yyyy-mm-dd)'
            ]
        ];
    }

    private function _getDeviceTypeValidationRule ($field)
    {
        return [
            'deviceType' => [
                'rule'    => ['isDeviceType'],
                'message' => $field . ' is not a valid device type.',
                'on'      => 'create'
            ]
        ];
    }

    public function isMonth ($month, $arg2 = 0)
    {
        return in_array($month['month'], [
            '01', '02', '03', '04', '05', '06',
            '07', '08', '09', '10', '11', '12'
        ]);
    }

    public function isYear ($year, $arg2 = 0)
    {
        $year = $year['year'];
        return is_numeric($year) && $year >= 2012 && $year <= (date('Y') + 1);
    }

    public function checkEmailExists ($email, $arg2 = null)
    {
        // If $args is array that means it came from a validation rule and get the id from the object data
        if (is_array($arg2)) {
            $userId = $this->data[$this->name]['id'];
        }
        elseif (is_numeric($arg2)) {
            $userId = $arg2;
        }
        else {
            throw new InvalidArgumentException(SwarmErrorCodes::setError('Argument 2 should either be an array of validation data or user id'));
        }

        if (!empty($userId)) {
            $user = $this->find('all', [
                'conditions' => [
                    'User.id !=' => $userId,
                    'User.email' => $email
                ]
            ]);
        }
        else {
            $user = $this->find('first', ['conditions' => ['User.email' => $email]]);
        }
        return empty($user);
    }

    public function checkUsernameExists ($username, $arg2 = 0)
    {
        if (is_array($arg2)) {
            $userId = $this->data[$this->name]['id'];
        }
        elseif (is_numeric($arg2)) {
            $userId = $arg2;
        }
        else {
            throw new InvalidArgumentException(
                SwarmErrorCodes::setError('Argument 2 should either be an array of validation data or user id')
            );
        }
        if (!empty($userId)) {
            $user = $this->find('all', [
                'conditions' => [
                    'User.id !='    => $userId,
                    'User.username' => $username
                ]
            ]);
        }
        else {
            $user = $this->find('first', ['conditions' => ['User.username' => $username]]);
        }
        return empty($user);
    }

    public function validateConfirmPassword ($checkField, $password)
    {

        $fieldName = '';
        foreach ($checkField as $key => $value) {
            $fieldName = $key;
            break;
        }
        return $this->data[$this->name][$password] === $this->data[$this->name][$fieldName];
    }

    public function isDeviceType ($deviceType, $arg2 = null)
    {       
        return in_array(strtolower($deviceType['type']), ['portal', 'presence', 'ping']);
    }

}
