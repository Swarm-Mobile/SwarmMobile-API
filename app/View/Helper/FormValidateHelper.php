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
class FormValidateHelper extends Helper
{

    public static function validate ($params, $rules)
    {
        if (!empty($rules)) {
            foreach ($rules as $param => $validators) {
                foreach ($validators as $validator) {
                    if (is_array($validator)) {
                        foreach ($validator as $key => $value) {
                            $method = $key . 'Validator';
                            self::$method($value, $param, strlen($params[$param]));
                        }
                    }
                    switch ($validator) {
                        case 'required':
                            if (empty($params[$param]))
                                throw new Exception("Param $param is required and isn't found on the request.", 501);
                            break;
                        case 'int':
                            if ((!is_numeric($params[$param])) || $params[$param] != (int) $params[$param])
                                throw new Exception("Param $param needs to be and int.", 501);
                            break;
                        case 'numeric':
                            if (!is_numeric($params[$param]))
                                throw new Exception("Param $param needs to be and int.", 501);
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

    private static function minLengthValidator ($min, $param, $value)
    {
        if ($value < $min) {
            throw new Exception('param_bad_length', "Param $param has to be at least $min characters long", 501);
        }
    }

}
