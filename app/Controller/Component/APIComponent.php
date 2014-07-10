<?php

require_once 'DBComponent.php';

class APIComponent {

    public $api;
    public $post_actions   = [];
    public $put_actions    = [];
    public $delete_actions = [];
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
    public $cache = true;
    public $rollups = true;
    public $archive_start_date = false;
    public $archive_end_date = false;
    public $request = false;

    public function __call($name, $arguments) {
        throw new APIException(404, 'endpoint_not_found', "The requested reference method don't exists");
    }

    public function __construct($request = false, $cache = true, $rollups = true) {
        $this->cache = $cache;
        $this->rollups = $rollups;
        $this->api = new APIController();
        $this->api->request = $request;
        $this->api->cache = $this->cache;
        $this->api->rollups = $this->rollups;

        $date = new DateTime(date('Y-m-01 00:00:00'));
        date_sub($date, date_interval_create_from_date_string('4 months'));
        $this->archive_end_date = date_format($date, 'Y-m-d');
        date_sub($date, date_interval_create_from_date_string('8 months'));
        $this->archive_start_date = date_format($date, 'Y-m-d');
    }

    public function archived($date) {
        $date = new DateTime($date);
        $last_archive = new DateTime($this->archive_end_date);
        return $date < $last_archive;
    }

    public function stored($date) {
        $date = new DateTime($date);
        $first_archive = new DateTime($this->archive_start_date);
        return $date >= $first_archive;
    }

    public static function validate($params, $rules) {
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
                            try {
                                $date = new DateTime($params[$param]);
                            } catch (Exception $e) {
                                throw new APIException(
                                501, 'param_bad_formatted', "Param $param needs to be a date."
                                );
                            }
                            $swarm_born = new DateTime('2013-01-01');
                            $max_date = new DateTime(date('Y-m-d'));
                            date_add($max_date, date_interval_create_from_date_string('10 days'));
                            if ($date < $swarm_born && $date < $max_date) {
                                throw new APIException(
                                501, 'param_bad_formatted', "Param $param needs to be a date greater than 2013-01-01 and lower than today."
                                );
                            }
                            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $params[$param])){
                                throw new APIException(
                                501, 'param_bad_formatted', "Param $param needs to have the yyyy-mm-dd format"
                                );
                            }
                            break;
                        case 'date_interval':
                            $start_date = new DateTime($params['start_date']);
                            $end_date = new DateTime($params['end_date']);
                            if ($start_date > $end_date) {
                                throw new APIException(
                                501, 'param_bad_formatted', "Param end_date needs to be greater than start_date"
                                );
                            }
                            break;
                        case 'year':
                            if (!is_numeric($params[$param]) || $params[$param] < 2013 || $params[$param] > date('Y') + 1) {
                                throw new APIException(
                                501, 'param_bad_formatted', "Param $param needs to be a valid year after 2012"
                                );
                            }
                            break;
                        case 'month':
                            if (!is_numeric($params[$param]) || $params[$param] < 1 || $params[$param] > 12) {
                                throw new APIException(
                                501, 'param_bad_formatted', "Param $param needs to be a valid month"
                                );
                            }
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

    public function countWorkDays($start_date, $end_date, $location_id) {
        $data = $this->api->internalCall('location', 'data', array('location_id' => $location_id));
        $end = new DateTime($end_date);
        $d = 0;
        $new_start_date = new DateTime($start_date);
        do {
            $weekday = strtolower($end->format('l'));
            if ($data['data'][$weekday . '_open'] != 0 && $data['data'][$weekday . '_close'] != 0) {
                $d++;
            }
            date_add($new_start_date, date_interval_create_from_date_string('1 days'));
        } while ($new_start_date <= $end);
        return $d;
    }

    public function getOpenCloseTimes($date, $data, $timezone) {
        $weekday = strtolower(date('l', strtotime($date)));

        $tzLocal = $this->getLocalTimezone($timezone);
        $timezone = $tzLocal->getName();

        $start_date = new DateTime($date . ' ' . $data['data'][$weekday . '_open'] . ':00', $tzLocal);
        $start_date = $start_date->setTimezone(new DateTimeZone('GMT'));
        $start_date = $start_date->format('Y-m-d H:i:s');

        $end_date = new DateTime($date . ' ' . $data['data'][$weekday . '_close'] . ':00', $tzLocal);
        $end_date = $end_date->setTimezone(new DateTimeZone('GMT'));
        $end_date = $end_date->format('Y-m-d H:i:s');

        return array($start_date, $end_date);
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

    public static function getNightClubTimezone($data) {
        if ($data['data']['nightclub_hours'] == 'yes') {
            switch ($data['data']['nightclub_hours_location']) {
                case 'eastcoast_time': return 'America/Detroit';
                case 'pacific_time': return 'America/Los_Angeles';
                case 'mountain_time': return 'America/Denver';
                case 'central_time': return 'America/Chicago';
                case 'eastaustralian_time': return 'Australia/Brisbane';
            }
        }
        return $data['data']['timezone'];
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

    public function locationOpenCompare($data, $timezone) {
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

    public function iterativeTotals($component, $method, $params) {
        $aResults = array();
        if ($params['start_date'] != $params['end_date']) {
            $end = new DateTime($params['end_date']);
            $slave_params = $params;
            do {
                $slave_params['end_date'] = $slave_params['start_date'];
                $result = $this->api->internalCall($component, $method, $slave_params);
                $aResults[] = $result;
                $new_start_date = new DateTime($slave_params['start_date']);
                date_add($new_start_date, date_interval_create_from_date_string('1 days'));
                $slave_params['start_date'] = date_format($new_start_date, 'Y-m-d');
            } while ($new_start_date <= $end);
            $result = array();
            foreach ($aResults as $cResult) {
                foreach ($cResult as $k => $v) {
                    @$result[$k] += $v;
                }
            }
            return $result;
        }
    }

    public function iterativeQuery($component, $method, $params) {
        $aResults = array();
        if ($params['start_date'] != $params['end_date']) {
            $end = new DateTime($params['end_date']);
            $slave_params = $params;
            do {
                $slave_params['end_date'] = $slave_params['start_date'];
                $result = $this->api->internalCall($component, $method, $slave_params);
                $aResults[] = $result;
                $new_start_date = new DateTime($slave_params['start_date']);
                date_add($new_start_date, date_interval_create_from_date_string('1 days'));
                $slave_params['start_date'] = date_format($new_start_date, 'Y-m-d');
            } while ($new_start_date <= $end);
            $result = array();
            foreach ($aResults as $cResult) {
                foreach ($cResult as $oRow) {
                    $result[] = $oRow;
                }
            }
            return $result;
        }
    }

    public function iterativeHourDateCall($component, $method, $params) {
        $aResults = array();
        if ($params['start_date'] != $params['end_date']) {
            $end = new DateTime($params['end_date']);
            $slave_params = $params;
            do {
                $slave_params['end_date'] = $slave_params['start_date'];
                $result = $this->api->internalCall($component, $method, $slave_params);
                $aResults[] = $result;
                $new_start_date = new DateTime($slave_params['start_date']);
                date_add($new_start_date, date_interval_create_from_date_string('1 days'));
                $slave_params['start_date'] = date_format($new_start_date, 'Y-m-d');
            } while ($new_start_date <= $end);
            return $this->mergeHourDateResults($aResults);
        }
    }

    public function iterativeCall($component, $method, $params) {
        $aResults = array();
        if ($params['start_date'] != $params['end_date']) {
            $end = new DateTime($params['end_date']);
            $slave_params = $params;
            do {
                $slave_params['end_date'] = $slave_params['start_date'];
                $result = $this->api->internalCall($component, $method, $slave_params);
                $aResults[] = $result;
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
                'breakdown' => array()
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

    public function mergeHourDateResults($aResults = array()) {
        $result = array(
            'data' => array(
                'totals' => array('open' => 0, 'close' => 0, 'total' => 0),
                'breakdown' => array()
            )
        );
        foreach ($aResults as $cResult) {
            if (!empty($cResult)) {
                $result['data']['totals']['open'] += $cResult['data']['totals']['open'] / count($aResults);
                $result['data']['totals']['close'] += $cResult['data']['totals']['close'] / count($aResults);
                $result['data']['totals']['total'] += $cResult['data']['totals']['total'] / count($aResults);
                if (!empty($cResult['data']['breakdown'])) {
                    foreach ($cResult['data']['breakdown'] as $date => $v) {
                        foreach ($v['hours'] as $hour => $values) {
                            $result['data']['breakdown'][$date]['hours'][$hour]['open'] = $values['open'];
                            @$result['data']['breakdown'][$date]['hours'][$hour]['total'] = $values['total'];
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
        $start_date = new DateTime($start_date . ' 00:00:00');
        $end_date = new DateTime($end_date . ' 23:59:59');
        while ($start_date <= $end_date) {
            $date = date_format($start_date, 'Y-m-d');
            $weekday = strtolower(date('l', strtotime($date)));
            foreach ($this->hours as $hour) {
                if (!isset($result['data']['breakdown'][$date]['hours'][$hour])) {
                    $open = (
                            $data['data'][$weekday . '_open'] != 0 &&
                            $data['data'][$weekday . '_close'] != 0 &&
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
            if (!isset($tmp['data']['breakdown'][$date]['totals']['isOpen'])) {
                $isOpen = $data['data'][$weekday . '_open'] != 0 && $data['data'][$weekday . '_close'] != 0;
                $tmp['data']['breakdown'][$date]['totals']['isOpen'] = $isOpen;
            }
            if (!isset($tmp['data']['breakdown'][$date]['totals']['close'])) {
                $tmp['data']['breakdown'][$date]['totals']['close'] = 0;
            }
            if (!isset($tmp['data']['breakdown'][$date]['totals']['total'])) {
                $tmp['data']['breakdown'][$date]['totals']['total'] = 0;
            }
            if (!isset($tmp['data']['breakdown'][$date]['totals']['open'])) {
                $tmp['data']['breakdown'][$date]['totals']['open'] = 0;
            }
            if (!isset($tmp['data']['breakdown'][$date]['totals'])) {
                $isOpen = $data['data'][$weekday . '_open'] != 0 && $data['data'][$weekday . '_close'] != 0;
                $tmp['data']['breakdown'][$date]['totals'] = array(
                    'open' => 0,
                    'close' => 0,
                    'total' => 0,
                    'isOpen' => $isOpen
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
        unset($tmp['data']['breakdown']['']);
        return $tmp;
    }

    public function countOpenHours($data) {
        $i = 0;
        $weekday = strtolower(date('l', strtotime($oRow[$t2]['date'])));
        if ($data['data'][$weekday . '_open'] != 0 && $data['data'][$weekday . '_close'] != 0) {
            return 0;
        }
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

    public function hourlyDailyFormat($aByDate, $aByHour, $data, $params, $endpoint, $t1, $t2, $dbAlias = 'value') {
        $cResult = array('data' => array('totals' => array('open' => 0, 'close' => 0, 'total' => 0)));
        $date = strtolower(date('Y-m-d', strtotime($params['start_date'])));
        $weekday = strtolower(date('l', strtotime($params['start_date'])));
        $tmp = array();
        $aByDate[0][0]['value'] = (empty($aByDate[0][0]['value'])) ? 0 : $aByDate[0][0]['value'];
        $aByDate[0][0]['value'] = (float) $aByDate[0][0]['value'];
        $cResult['data']['breakdown'][$date]['totals']['isOpen'] = false;
        foreach ($aByHour as $oRow) {
            $hour = $oRow[$t2]['hour'];
            $cValue = $oRow[$t1][$dbAlias];
            if ($hour < 10) {
                $hour = '0' . $hour;
            }
            if (
                    $data['data'][$weekday . '_open'] != 0 &&
                    $data['data'][$weekday . '_close'] != 0 &&
                    (int) $hour >= (int) strstr($data['data'][$weekday . '_open'], ':', true) &&
                    (int) $hour <= (int) strstr($data['data'][$weekday . '_close'], ':', true)
            ) {
                $cResult['data']['breakdown'][$date]['totals']['isOpen'] = true;
                $cResult['data']['breakdown'][$date]['hours'][$hour]['open'] = true;
                @$cResult['data']['breakdown'][$date]['totals']['open'] += $cValue;
                @$cResult['data']['breakdown'][$date]['totals']['close'] += 0;
                @$cResult['data']['breakdown'][$date]['totals']['total'] = $aByDate[0][0]['value'];
                @$cResult['data']['totals']['total'] = $aByDate[0][0]['value'];
                @$cResult['data']['totals']['open'] += $cValue;
                @$cResult['data']['totals']['close'] += 0;
            } else {
                $cResult['data']['breakdown'][$date]['hours'][$hour]['open'] = false;
                @$cResult['data']['breakdown'][$date]['totals']['open'] += 0;
                @$cResult['data']['breakdown'][$date]['totals']['close'] += $cValue;
                @$cResult['data']['breakdown'][$date]['totals']['total'] = $aByDate[0][0]['value'];
                @$cResult['data']['totals']['total'] = $aByDate[0][0]['value'];
                @$cResult['data']['totals']['open'] += 0;
                @$cResult['data']['totals']['close'] += $cValue;
            }
            $cResult['data']['breakdown'][$date]['hours'][$hour]['total'] = $cValue;
        }
        $cResult['options'] = array(
            'endpoint' => $endpoint,
            'location_id' => $params['location_id'],
            'start_date' => $params['start_date'],
            'end_date' => $params['end_date'],
        );
        unset($cResult['breakdown']['data']['']);
        $result = $this->fillBlanks($cResult, $data, $params['start_date'], $params['end_date']);
        return $this->nightClubFormat($result, $data);
    }

    public static function nightClubFormat($result, $data) {
        if (@$data['data']['nightclub_hours'] == 'yes') {
            $ncResult = array();
            $ncResult['options'] = $result['options'];
            $ncResult['data']['totals'] = $result['data']['totals'];
            foreach ($result['data']['breakdown'] as $date => $values) {
                $ncResult['data']['breakdown'][$date]['totals'] = $values['totals'];
                foreach ($values['hours'] as $h => $v) {
                    $tzLocal = self::getLocalTimezone($data['data']['timezone']);
                    $tzNC = self::getNightClubTimezone($data);
                    $tmp = new DateTime("2014-01-01 $h:00:00", $tzLocal);
                    $tmp = $tmp->setTimezone(new DateTimeZone($tzNC));
                    $h = $tmp->format('H');
                    $ncResult['data']['breakdown'][$date]['hours'][$h] = $v;
                }
            }
            return $ncResult;
        }
        return $result;
    }

    /**
     * Formats results array for use on the dashboard
     * @param $aRes Results array to format
     * @param $data location data
     * @param $params Parameters that generated the results
     * @param $start_date
     * @param $end_date
     * @param $endpoint Which API function generated these results
     * @param $t1 The array index containing the value in the results array
     * @param $t2 The array index containing the dates in the results array.  Defaults to t1.
     * @param string $dbAlias The index within each row array with the value being counted.  Defaults to 'value'
     * @return array
     */
    public function format($aRes, $data, $params, $endpoint, $t1, $t2, $dbAlias = 'value') {
        $cResult = array('data' => array('totals' => array('open' => 0, 'close' => 0, 'total' => 0)));
        foreach ($aRes as $oRow) {
            $weekday = strtolower(date('l', strtotime($oRow[$t2]['date'])));
            $date = $oRow[$t2]['date'];
            $hour = $oRow[$t2]['hour'];
            $cValue = $oRow[$t1][$dbAlias];
            $isOpen = $data['data'][$weekday . '_open'] != 0 && $data['data'][$weekday . '_close'] != 0;
            if ($hour < 10) {
                $hour = '0' . $hour;
            }
            if (
                    (int) $hour >= (int) strstr($data['data'][$weekday . '_open'], ':', true) &&
                    (int) $hour <= (int) strstr($data['data'][$weekday . '_close'], ':', true)
            ) {
                $cResult['data']['breakdown'][$date]['hours'][$hour]['open'] = true;
                @$cResult['data']['breakdown'][$date]['totals']['open'] += $cValue;
                @$cResult['data']['breakdown'][$date]['totals']['close'] += 0;
                @$cResult['data']['breakdown'][$date]['totals']['isOpen'] = $isOpen;
                @$cResult['data']['totals']['open'] += $cValue;
            } else {
                $cResult['data']['breakdown'][$date]['hours'][$hour]['open'] = false;
                @$cResult['data']['breakdown'][$date]['totals']['open'] += 0;
                @$cResult['data']['breakdown'][$date]['totals']['close'] += $cValue;
                @$cResult['data']['breakdown'][$date]['totals']['isOpen'] = $isOpen;
                @$cResult['data']['totals']['close'] += $cValue;
            }
            @$cResult['data']['breakdown'][$date]['totals']['total'] += $cValue;
            @$cResult['data']['breakdown'][$date]['hours'][$hour]['total'] += $cValue;
            @$cResult['data']['totals']['total'] += $cValue;
        }
        $cResult['options'] = array(
            'endpoint' => $endpoint,
            'location_id' => $params['location_id'],
            'start_date' => $params['start_date'],
            'end_date' => $params['end_date'],
        );
        unset($cResult['breakdown']['data']['']);
        $result = $this->fillBlanks($cResult, $data, $params['start_date'], $params['end_date']);
        return $this->nightClubFormat($result, $data);
    }

    public function averagify($result, $data, $total = true) {
        $num_days = count($result['data']['breakdown']);
        $total_hours = 0;
        if ($num_days == 1) {
            foreach ($result['data']['breakdown'] as $date => $values) {
                $num_hours = $this->countRevenueHours($result, $date);
                $total_hours += $num_hours;
                foreach ($values['totals'] as $k => $v) {
                    if ($k != 'isOpen' && ($k != 'total' || $k == 'total' && $total)) {
                        $result['data']['breakdown'][$date]['totals'][$k] = ($num_hours == 0) ? 0 : round($v / $num_hours, 2);
                    } else {
                        $result['data']['breakdown'][$date]['totals'][$k] = $v;
                    }
                }
            }
            foreach ($result['data']['totals'] as $k => $v) {
                if ($k != 'total' || $k == 'total' && $total) {
                    $result['data']['totals'][$k] = ($total_hours == 0) ? 0 : round($v / $total_hours, 2);
                }
            }
        } else {
            foreach ($result['data']['totals'] as $k => $v) {
                if ($k != 'total' || $k == 'total' && $total) {
                    $result['data']['totals'][$k] = round($v / $num_days, 2);
                }
            }
        }
        unset($result['breakdown']['data']['']);
        return $result;
    }

    public function percentify($aRes1, $aRes2) {
        $result = array();
        foreach ($aRes1['data']['breakdown'] as $date => $values) {
            foreach ($values['hours'] as $hour => $v) {
                $a = @$aRes1['data']['breakdown'][$date]['hours'][$hour]['total'];
                $b = @$aRes2['data']['breakdown'][$date]['hours'][$hour]['total'];
                $result['data']['breakdown'][$date]['hours'][$hour]['total'] = ($b == 0) ? 0.00 : round(($a / $b) * 100, 2);
                $result['data']['breakdown'][$date]['hours'][$hour]['open'] = $v['open'];
                if ($result['data']['breakdown'][$date]['hours'][$hour]['total'] > 100) {
                    $result['data']['breakdown'][$date]['hours'][$hour]['total'] = 100;
                }
            }
            foreach ($values['totals'] as $k => $v) {
                if ($k != 'isOpen') {
                    $a = @$aRes1['data']['breakdown'][$date]['totals'][$k];
                    $b = @$aRes2['data']['breakdown'][$date]['totals'][$k];
                    $result['data']['breakdown'][$date]['totals'][$k] = ($b == 0) ? 0.00 : round(($a / $b) * 100, 2);
                } else {
                    $result['data']['breakdown'][$date]['totals'][$k] = $v;
                }
            }
        }
        foreach ($aRes1['data']['totals'] as $k => $v) {
            $a = @$aRes1['data']['totals'][$k];
            $b = @$aRes2['data']['totals'][$k];
            $result['data']['totals'][$k] = ($b == 0) ? 0.00 : round(($a / $b) * 100, 2);
            if ($result['data']['totals'][$k] > 100) {
                $result['data']['totals'][$k] = 100;
            }
        }
        unset($result['breakdown']['data']['']);
        return $result;
    }

    public function calculate($aRes1, $aRes2, $hours = false) {
        $result = $aRes1;
        foreach ($aRes1['data']['breakdown'] as $date => $values) {
            if ($hours) {
                foreach ($values['hours'] as $hour => $v) {
                    $a = @$aRes1['data']['breakdown'][$date]['hours'][$hour]['total'];
                    $b = @$aRes2['data']['breakdown'][$date]['hours'][$hour]['total'];
                    $result['data']['breakdown'][$date]['hours'][$hour]['total'] = ($b == 0) ? 0.00 : round($a / $b, 2);
                    $result['data']['breakdown'][$date]['hours'][$hour]['open'] = $v['open'];
                }
            }
            foreach ($values['totals'] as $k => $v) {
                if ($k != 'isOpen') {
                    $a = @$aRes1['data']['breakdown'][$date]['totals'][$k];
                    $b = @$aRes2['data']['breakdown'][$date]['totals'][$k];
                    $result['data']['breakdown'][$date]['totals'][$k] = ($b == 0) ? 0 : round($a / $b, 2);
                } else {
                    $result['data']['breakdown'][$date]['totals'][$k] = $v;
                }
            }
        }
        foreach ($aRes1['data']['totals'] as $k => $v) {
            $a = @$aRes1['data']['totals'][$k];
            $b = @$aRes2['data']['totals'][$k];
            $result['data']['totals'][$k] = ($b == 0) ? 0 : round($a / $b, 2);
        }
        unset($result['breakdown']['data']['']);
        return $result;
    }

    public function getSessionsTableName($start_time, $end_time, $location_id, $ap_id) {
        $start_date = substr($start_time, 0, 10);
        $end_date = substr($end_time, 0, 10);
        $suffix = $location_id . '_' . str_replace('-', '_', $start_date . '_' . $end_date);
        $tmp_table = 'sessions_' . $suffix;
        $table = ($this->archived($start_date)) ? 'sessions_archive' : 'sessions';
        return $table;
    }

}
