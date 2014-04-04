<?php

class APIComponent extends Component {

    public function validate($params, $rules) {        
        foreach ($rules as $param => $validators) {
            foreach ($validators as $validator) {
                switch ($validator) {
                    case 'required':
                        if (empty($params[$param])){
                            throw new APIException(
                                501, 
                                'required_param_not_found', 
                                "Param $param is required and isn't found on the request."
                            );
                        }
                        break;
                    case 'int':
                        if (($params[$param]) != (int)$params[$param]){
                            throw new APIException(
                                501, 
                                'param_bad_formatted', 
                                "Param $param needs to be and int."
                            );
                        }
                        break;
                    case 'numeric':
                        if (!is_numeric($params[$param])){
                            throw new APIException(
                                501, 
                                'param_bad_formatted', 
                                "Param $param needs to be and int."
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
