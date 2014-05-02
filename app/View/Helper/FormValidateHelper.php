<?php
App::uses('Helper', 'View');

/**
 * String helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class FormValidateHelper extends Helper {
	public static function validate($params, $rules) {
	    if (!empty($rules)) {
	        foreach ($rules as $param => $validators) {
	            foreach ($validators as $validator) {
	            	if (is_array($validator)) {
	            		foreach ($validator as $key=>$value) {
	            			$method = $key.'Validator';
	                        self::$method($value, $param, strlen($params[$param]));
	            		}
	            	}
	                switch ($validator) {
	                    case 'required':
	                        if (empty($params[$param])) {
	                            throw new APIException(
	                            501, 'required_param_not_found', "Param $param is required and isn't found on the request."
	                            );
	                        }
	                        break;
	                    case 'int':
	                        if ((!is_numeric($params[$param])) || $params[$param] != (int) $params[$param]) {
	                            throw new APIException(
	                            501, 'param_bad_formatted', "Param $param needs to be and int."
	                            );
	                        }
	                        break;
	                    case 'numeric':
	                        if (!is_numeric($params[$param])) {
	                            throw new APIException(
	                            501, 'param_bad_formatted', "Param $param needs to be and int."
	                            );
	                        }
	                        break;
	                    case 'date':
	                        //TODO:
	                        break;
	                    case 'datetime':
	                        //TODO:
	                        break;
	                }
	            }
	        }
	    }
	}

    private static function minLengthValidator($min, $param, $value) {
    	if ($value < $min) {
    		throw new APIException(501, 'param_bad_length', "Param $param has to be at least $min characters long"); 
    	}
    }
}