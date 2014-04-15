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
        if (!isset($tmp['data']['totals'])) {
            $tmp['data']['totals'] = array(
                'open' => 0,
                'total' => 0,
                'close' => 0
            );
        }
        return $tmp;
    }
    public function countOpenHours($data) {
        $i = 0;
        $weekday = strtolower(date('l', strtotime($oRow[$t2]['date'])));
        $open_hour = (int) strstr($data['data'][$weekday . '_open'], ':', true);
        $close_hour = (int) strstr($data['data'][$weekday . '_close'], ':', true);
        return ($close_hour - $open_hour) + 1;
    }
    public function countRevenueHours($result, $date) {
        $i = 0;
        foreach ($result['data']['breakdown'][$date]['hours'] as $hour => $v) {
            $i += ($v['total'] > 0 && $v['open']) ? 1 : 0;
        }
        return $i;
    }
    public function hourlyDailyFormat($aByDate, $aByHour, $data, $params, $start_date, $end_date, $endpoint, $t1, $t2, $dbAlias = 'value') {
        $cResult = array();
        $date = strtolower(date('Y-m-d', strtotime($start_date)));
        foreach ($aByHour as $oRow) {
            $weekday = strtolower(date('l', strtotime($start_date)));
            $hour = $oRow[$t2]['hour'];
            $cValue = $oRow[$t1][$dbAlias];
            if ($hour < 10) {
                $hour = '0' . $hour;
            }
            if (
                    (int) $hour >= (int) strstr($data['data'][$weekday . '_open'], ':', true) &&
                    (int) $hour <= (int) strstr($data['data'][$weekday . '_close'], ':', true)
            ) {
                $cResult['data']['breakdown'][$date]['hours'][$hour]['open'] = true;
                @$cResult['data']['breakdown'][$date]['totals']['open'] += $cValue;
            } else {
                $cResult['data']['breakdown'][$date]['hours'][$hour]['open'] = false;
                @$cResult['data']['breakdown'][$date]['totals']['close'] += $cValue;
            }
            @$cResult['data']['breakdown'][$date]['hours'][$hour]['total'] += $cValue;
        }
        $aByDate[0][0]['value'] = (empty($aByDate[0][0]['value'])) ? 0 : $aByDate[0][0]['value'];
        @$cResult['data']['breakdown'][$date]['totals']['total'] = $aByDate[0][0]['value'];
        @$cResult['data']['totals']['total'] = $aByDate[0][0]['value'];
        @$cResult['data']['totals']['open']  = $aByDate[0][0]['value'];
        @$cResult['data']['totals']['close'] = 0;
        $cResult['options'] = array(
            'endpoint' => $endpoint,
            'member_id' => $params['member_id'],
            'start_date' => $params['start_date'],
            'end_date' => $params['end_date'],
        );
        return $this->fillBlanks($cResult, $data, $start_date, $end_date);
    }
    public function format($aRes, $data, $params, $start_date, $end_date, $endpoint, $t1, $t2, $dbAlias = 'value') {
        $cResult = array();
        foreach ($aRes as $oRow) {
            $weekday = strtolower(date('l', strtotime($oRow[$t2]['date'])));
            $date = $oRow[$t2]['date'];
            $hour = $oRow[$t2]['hour'];
            $cValue = $oRow[$t1][$dbAlias];
            if ($hour < 10) {
                $hour = '0' . $hour;
            }
            if (
                    (int) $hour >= (int) strstr($data['data'][$weekday . '_open'], ':', true) &&
                    (int) $hour <= (int) strstr($data['data'][$weekday . '_close'], ':', true)
            ) {
                $cResult['data']['breakdown'][$date]['hours'][$hour]['open'] = true;
                @$cResult['data']['breakdown'][$date]['totals']['open'] += $cValue;
                @$cResult['data']['totals']['open'] += $cValue;
            } else {
                $cResult['data']['breakdown'][$date]['hours'][$hour]['open'] = false;
                @$cResult['data']['breakdown'][$date]['totals']['close'] += $cValue;
                @$cResult['data']['totals']['close'] += $cValue;
            }
            @$cResult['data']['breakdown'][$date]['totals']['total'] += $cValue;
            @$cResult['data']['breakdown'][$date]['hours'][$hour]['total'] += $cValue;
            @$cResult['data']['totals']['total'] += $cValue;
        }
        $cResult['options'] = array(
            'endpoint' => $endpoint,
            'member_id' => $params['member_id'],
            'start_date' => $params['start_date'],
            'end_date' => $params['end_date'],
        );
        return $this->fillBlanks($cResult, $data, $start_date, $end_date);
    }
    public function averagify($result, $data) {
        $num_days = count($result['data']['breakdown']);
        $total_hours = 0;
        if ($num_days == 1) {
            foreach ($result['data']['breakdown'] as $date => $values) {
                $num_hours = $this->countRevenueHours($result, $date);
                $total_hours += $num_hours;
                foreach ($values['totals'] as $k => $v) {
                    $result['data']['breakdown'][$date]['totals'][$k] = ($num_hours == 0) ? 0 : round($v / $num_hours, 2);
                }
            }
            foreach ($result['data']['totals'] as $k => $v) {
                $result['data']['totals'][$k] = ($total_hours == 0) ? 0 : round($v / $total_hours, 2);
            }
        } else {
            foreach ($result['data']['totals'] as $k => $v) {
                $result['data']['totals'][$k] = ($total_hours == 0) ? 0 : round($v / $num_days, 2);
            }
        }
        return $result;
    }
    public function percentify($aRes1, $aRes2) {
        $result = array();
        foreach ($aRes1['data']['breakdown'] as $date => $values) {
            foreach ($values['hours'] as $hour => $v) {
                foreach ($v as $i => $j) {
                    $a = $aRes1['data']['breakdown'][$date]['hours'][$hour][$i];
                    $b = $aRes2['data']['breakdown'][$date]['hours'][$hour][$i];
                    $result['data']['breakdown'][$date]['hours'][$hour][$i] = ($b == 0) ? 0.00 : round(($a / $b) * 100, 2);
                }
            }
            foreach ($values['totals'] as $k => $v) {
                $a = $aRes1['data']['breakdown'][$date]['totals'][$k];
                $b = $aRes2['data']['breakdown'][$date]['totals'][$k];
                $result['data']['breakdown'][$date]['totals'][$k] = ($b == 0) ? 0.00 : round(($a / $b) * 100, 2);
            }
        }
        foreach ($aRes1['data']['totals'] as $k => $v) {
            $a = $aRes1['data']['totals'][$k];
            $b = $aRes2['data']['totals'][$k];
            $result['data']['totals'][$k] = ($b == 0) ? 0.00 : round(($a / $b) * 100, 2);
        }
        return $result;
    }
    public function calculate($aRes1, $aRes2, $hours = false) {
        $result = $aRes1;
        foreach ($aRes1['data']['breakdown'] as $date => $values) {
            if ($hours) {
                foreach ($values['hours'] as $hour => $v) {
                    foreach ($v as $i => $j) {
                        $a = $aRes1['data']['breakdown'][$date]['hours'][$hour][$i];
                        $b = $aRes2['data']['breakdown'][$date]['hours'][$hour][$i];
                        $result['data']['breakdown'][$date]['hours'][$hour][$i] = ($b == 0) ? 0.00 : round($a / $b, 2);
                    }
                }
            }
            foreach ($values['totals'] as $k => $v) {
                $a = $aRes1['data']['breakdown'][$date]['totals'][$k];
                $b = $aRes2['data']['breakdown'][$date]['totals'][$k];
                $result['data']['breakdown'][$date]['totals'][$k] = ($b == 0) ? 0 : round($a / $b, 2);
            }
        }
        foreach ($aRes1['data']['totals'] as $k => $v) {
            $a = $aRes1['data']['totals'][$k];
            $b = $aRes2['data']['totals'][$k];
            $result['data']['totals'][$k] = ($b == 0) ? 0 : round($a / $b, 2);
        }
        return $result;
    }

}
