<?php

class APIComponent {

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
    public $hours = array(
        '00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11',
        '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'
    );

    public function __construct() {
        $this->api = new APIController();
    }

    public function validate($params, $rules) {
        if (!empty($rules)) {
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
        return $return . ')';
    }

    public function registerFilter($data) {
        return (!empty($data['data']['register_filter'])) ? "register_id='{$data['data']['register_filter']}' AND" : '';
    }

    public function outletFilter($data) {
        return (!empty($data['data']['outlet_filter'])) ? "outlet_id='{$data['data']['outlet_filter']}' AND" : '';
    }

    public function iterativeCall($component, $method, $params) {
        $aResults = array();
        if ($params['start_date'] != $params['end_date']) {
            $end = new DateTime($params['end_date']);
            $slave_params = $params;
            do {
                $slave_params['end_date'] = $slave_params['start_date'];
                $aResults[] = $this->api->internalCall($component, $method, $slave_params);
                $new_start_date = new DateTime($slave_params['start_date']);
                date_add($new_start_date, date_interval_create_from_date_string('1 days'));
                $slave_params['start_date'] = date_format($new_start_date, 'Y-m-d');
            } while ($new_start_date <= $end);
            return $this->mergeResults($aResults);
        }
    }

    public function mergeResults($aResults = array()) {
        $result = array(
            'data' => array(
                'totals' => array('open' => 0, 'close' => 0, 'total' => 0),
                'breakdown' => array(),
                'options' => array()
            )
        );
        foreach ($aResults as $cResult) {
            if (!empty($cResult)) {
                $result['data']['totals']['open'] += $cResult['data']['totals']['open'];
                $result['data']['totals']['close'] += $cResult['data']['totals']['close'];
                $result['data']['totals']['total'] += $cResult['data']['totals']['total'];
                if (!empty($cResult['data']['breakdown'])) {
                    foreach ($cResult['data']['breakdown'] as $date => $v) {
                        foreach ($v['hours'] as $hour => $values) {
                            $result['data']['breakdown'][$date]['hours'][$hour]['open'] = $values['open'];
                            @$result['data']['breakdown'][$date]['hours'][$hour]['total'] += $values['total'];
                        }
                        foreach ($v['totals'] as $k => $j) {
                            @$result['data']['breakdown'][$date]['totals'][$k] += $j;
                        }
                    }
                }
            }
        }
        $result['options'] = $cResult['options'];
        $result['options']['start_date'] = $aResults[0]['options']['start_date'];
        return $result;
    }

    public function fillBlanks($result, $data, $start_date, $end_date) {
        $tmp = $result;
        $start_date = new DateTime($start_date);
        $end_date = new DateTime($end_date);
        while ($start_date <= $end_date) {
            $date = date_format($start_date, 'Y-m-d');
            foreach ($this->hours as $hour) {
                if (!isset($result['data']['breakdown'][$date]['hours'][$hour])) {
                    $weekday = strtolower(date('l', strtotime($date)));
                    $open = (
                            (int) $hour >= (int) strstr($data['data'][$weekday . '_open'], ':', true) &&
                            (int) $hour <= (int) strstr($data['data'][$weekday . '_close'], ':', true)
                            );
                    $tmp['data']['breakdown'][$date]['hours'][$hour] = array(
                        'open' => $open,
                        'total' => 0
                    );
                }
            }
            ksort($tmp['data']['breakdown'][$date]['hours']);
            if (!isset($tmp['data']['breakdown'][$date]['totals'])) {
                $tmp['data']['breakdown'][$date]['totals'] = array(
                    'open' => 0,
                    'close' => 0,
                    'total' => 0
                );
            }
            date_add($start_date, date_interval_create_from_date_string('1 days'));
        }
        if(!isset($tmp['data']['totals'])){
            $tmp['data']['totals'] = array(
                'open' => 0,
                'total' => 0,
                'close' => 0
            );
        }
        return $tmp;
    }
    
    public function countOpenHours($data){
        $i = 0;
        $weekday = strtolower(date('l', strtotime($oRow[$t2]['date'])));
        $open_hour = (int) strstr($data['data'][$weekday . '_open'], ':', true);
        $close_hour = (int) strstr($data['data'][$weekday . '_close'], ':', true);
        return ($close_hour - $open_hour)+1;        
    }
    
    public function countRevenueHours($result,$date){
        $i = 0;           
        foreach($result['data']['breakdown'][$date]['hours'] as $hour=>$v){
            $i += ($v['total']>0 && $v['open'])?1:0;
        }
        return $i;
    }

    public function averagify($result, $data) {       
        $num_days = count($result['data']['breakdown']);        
        $total_hours = 0;
        if ($num_days == 1) {
            foreach ($result['data']['breakdown'] as $date => $values) {                
                //$num_hours = $this->countOpenHours($data);                
                $num_hours = $this->countRevenueHours($result, $date);                
                $total_hours += $num_hours;                
                foreach ($values['totals'] as $k => $v) {
                    $result['data']['breakdown'][$date]['totals'][$k] = round($v / $num_hours,2);
                }                
                foreach($values['hours'] as $h => $v){                    
                    foreach($v as $i => $j){                        
                        $result['data']['breakdown'][$date]['hours'][$h][$i] = is_numeric($j)?round($j,2):$j;
                    }
                }
            }
            foreach ($result['data']['totals'] as $k => $v) {
                $result['data']['totals'][$k] = round($v / $total_hours,2);
            }            
        } else {
            foreach ($result['data']['totals'] as $k => $v) {
                $result['data']['totals'][$k] = round($v / $num_days,2);
            }
        }        
        return $result;
    }

}
