<?php

App::uses('Controller', 'Controller');
App::uses('ValidatorComponent', 'Controller/Component');

class AppController extends Controller
{   
    
    public function validateParams ($params, $rules)
    {
        $return = [];        
        foreach ($rules as $k => $validators) {                   
            if (in_array('required', $validators)) {
                if (!isset($params[$k])) {                    
                    throw new Exception($k . ' is required');        
                }
            }
            if (isset($params[$k])) {
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
                                if (!$valid){
                                    throw new Exception($k . ' must be a valid ' . $validator);
                                }
                        }
                    }
                    $return[] = $params[$k];
                } else {
                    $return[] = '';
                }
            }
            else {
                $return[] = '';
            }        
        }
        return $return;
    }

}
