<?php

class APIComponent extends Component {

    public $api;
    public static $TZ_CORRECTIONS = array(
        'Austrailia NSW' => 'Australia/NSW',
        'Australia NSW' => 'Australia/NSW',
        'Australia/Syndey' => 'Australia/Sydney',
        'Europe/Amsterdam ' => 'Europe/Amsterdam',
        '' => 'America/Los_Angeles'
    );

    public function __construct(\ComponentCollection $collection, $settings = array()) {
        parent::__construct($collection, $settings);
        $this->api = new APIController();
    }

    public function validate($params, $rules) {
        foreach ($rules as $param => $validators) {
            foreach ($validators as $validator) {
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

    public function getGroupByType($params) {
        if ((!isset($params['group_by'])) ||
                (!in_array($params['group_by'], array('hour', 'date')))) {
            return ($params['start_date'] == $params['end_date']) ? 'hours' : 'date';
        }
        return $params['group_by'];
    }

    public static function getLocalTimezone($tzName) {
        $timezone = trim($tzName);
        try {
            $tzLocal = new DateTimeZone($timezone);
        } catch (Exception $e) {
            $tzLocal = isset(self::$TZ_CORRECTIONS[$timezone]) ? 
                new DateTimeZone(self::$TZ_CORRECTIONS[$timezone]) : 
                new DateTimeZone('America/Los_Angeles');
        }
        return $tzLocal;
    }

}
