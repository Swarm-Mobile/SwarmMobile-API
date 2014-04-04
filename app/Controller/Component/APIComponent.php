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
    public $timezone = 'America/Los_Angeles';
    public $weekdays = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');

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

    public function parseDates($params, $timezone) {
        $tzLocal = $this->getLocalTimezone($timezone);
        $timezone = $tzLocal->getName();

        $start_date = new DateTime($params['start_date'] . ' 00:00:00', $tzLocal);
        $start_date = $start_date->setTimezone(new DateTimeZone('GMT'));
        $start_date = $start_date->format('Y-m-d H:i:s');

        $end_date = new DateTime($params['end_date'] . ' 23:59:59', $tzLocal);
        $end_date = $end_date->setTimezone(new DateTimeZone('GMT'));
        $end_date = $end_date->format('Y-m-d H:i:s');

        return array($start_date, $end_date, $timezone);
    }

    public function getLocalTimezone($tzName) {
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

    public function storeOpenCompare($data, $timezone) {        
        $return = "(";
        $i = 0;
        $or = '';        
        foreach ($this->weekdays as $day) {
            $i++;
            $open = $data['data'][$day . '_open'];
            $close = $data['data'][$day . '_close'];
            $tmp = <<<SQL
$or
(DAYOFWEEK(DATE(CONVERT_TZ(time_login,'GMT', '$timezone'))) = $i )
AND DATE_FORMAT(CONVERT_TZ(time_login,'GMT', '$timezone'), '%H:%i') >= '$open'
AND DATE_FORMAT(CONVERT_TZ(time_login,'GMT', '$timezone'), '%H:%i') <= '$close'
AND DATE_FORMAT(convert_tz(time_logout,'GMT', '$timezone') - INTERVAL 1 HOUR, '%H:%i') <= '$close'
SQL;
            $or = ' OR ';
            $return .= $tmp;
        }        
        return $return.')';
    }

}
