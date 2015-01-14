<?php

App::uses('ValidatorComponent','Controller/Component');

class MetricFormatComponent
{

    private static $baseFormat = [
        'breakdown' => [],
        'totals'    => [
            'open'  => 0,
            'close' => 0,
            'total' => 0
        ]
    ];

    public static function emptyHistoricalTotals ()
    {
        return [
            "revenue"                  => 0,
            "transactions"             => 0,
            "visitors"                 => 0,
            "conversionRate"           => 0,
            "avgTransactionsDaily"     => 0,
            "avgTransactionsWeekly"    => 0,
            "avgTransactionsMonthly"   => 0,
            "avgRevenueDaily"          => 0,
            "avgRevenueWeekly"         => 0,
            "avgRevenueMonthly"        => 0,
            "avgVisitorsDaily"         => 0,
            "avgVisitorsWeekly"        => 0,
            "avgVisitorsMonthly"       => 0,
            "avgConversionRateDaily"   => 0,
            "avgConversionRateWeekly"  => 0,
            "avgConversionRateMonthly" => 0
        ];
    }

    public static function emptyMonthlyTotals ()
    {
        return [
            'breakdown' => [],
            'totals'    => [
                'revenue'                => 0,
                'visitors'               => 0,
                'conversionRate'         => 0,
                'avgRevenueDaily'        => 0,
                'avgVisitorsDaily'       => 0,
                'avgConversionRateDaily' => 0,
            ]
        ];
    }

    public static function emptyDayResult ($locationId, $date)
    {
        $return = [
            'date'        => $date,
            'location_id' => $locationId,
            'total_open'  => 0,
            'total_close' => 0,
            'total_total' => 0,
            'ts_creation' => date('Y-m-d H:i:s'),
            'ts_update'   => date('Y-m-d H:i:s'),
        ];
        for ($i = 0; $i < 24; $i++) {
            $hour          = 'h' . (($i < 10) ? '0' : '') . $i;
            $return[$hour] = 0;
        }
        return $return;
    }

    public static function emptyDayTotalResult ($locationId, $date)
    {
        return [
            'date'                   => $date,
            'location_id'            => $locationId,
            'walkbys'                => 0,
            'sensorTraffic'          => 0,
            'transactions'           => 0,
            'revenue'                => 0,
            'totalItems'             => 0,
            'returning'              => 0,
            'footTraffic'            => 0,
            'presenceTraffic'        => 0,
            'portalTraffic'          => 0,
            'timeInShop'             => 0,
            'traffic'                => 0,
            'devices'                => 0,
            'itemsPerTransaction'    => 0,
            'windowConversion'       => 0,
            'avgTicket'              => 0,
            'conversionRate'         => 0,
            'presenceConversionRate' => 0,
            'portalConversionRate'   => 0,
            'dwell'                  => 0,
            'ts_creation'            => date('Y-m-d H:i:s'),
            'ts_update'              => date('Y-m-d H:i:s')
        ];
    }

    public static function formatAsRatio ($startDate, $endDate, $baseResultset, $divisorResultset, $openHours)
    {
        $return = self::$baseFormat;
        $date   = new DateTime($startDate);
        $end    = new DateTime($endDate);
        $openB = $closeB = $totalB = 0;
        $openD = $closeD = $totalD = 0;
        while ($date <= $end) {
            $ymd        = $date->format('Y-m-d');
            $baseRow    = isset($baseResultset[$ymd]) ? $baseResultset[$ymd] : (self::emptyDayResult(0, $date));
            $divisorRow = isset($divisorResultset[$ymd]) ? $divisorResultset[$ymd] : (self::emptyDayResult(0, $date));
            $weekday    = strtolower($date->format('l'));
            $isOpen     = $openHours[$weekday]['isOpen'];
            $open       = (int) strstr($openHours[$weekday]['open'], ':', true);
            $close      = (int) strstr($openHours[$weekday]['close'], ':', true);
            for ($i = 0; $i < 24; $i++) {
                $hour                                      = (($i < 10) ? '0' : '') . $i;
                $return['breakdown'][$ymd]['hours'][$hour] = [
                    'open'  => $isOpen && $i >= $open && $i <= $close,
                    'total' => self::ratio($baseRow['h' . $hour], $divisorRow['h' . $hour])
                ];
            }
            $return['breakdown'][$ymd]['totals']['open']  = self::ratio($baseRow['total_open'], $divisorRow['total_open']);
            $return['breakdown'][$ymd]['totals']['close'] = self::ratio($baseRow['total_close'], $divisorRow['total_close']);
            $return['breakdown'][$ymd]['totals']['total'] = self::ratio($baseRow['total_total'], $divisorRow['total_total']);
            $openB  += $baseRow['total_open'];
            $openD  += $divisorRow['total_open'];
            $closeB += $baseRow['total_close'];
            $closeD += $divisorRow['total_close'];
            $totalB += $baseRow['total_total'];
            $totalD += $divisorRow['total_total'];
            date_add($date, date_interval_create_from_date_string('1 days'));
        }
        $return['totals']['open'] += self::ratio($openB, $openD);
        $return['totals']['close'] += self::ratio($closeB, $closeD);
        $return['totals']['total'] += self::ratio($totalB, $totalD);        
        return $return;
    }

    public static function formatAsRate ($startDate, $endDate, $baseResultset, $divisorResultset, $openHours)
    {
        $return = self::$baseFormat;
        $date   = new DateTime($startDate);
        $end    = new DateTime($endDate);
        $openB = $closeB = $totalB = 0;
        $openD = $closeD = $totalD = 0;
        while ($date <= $end) {
            $ymd        = $date->format('Y-m-d');
            $baseRow    = isset($baseResultset[$ymd]) ? $baseResultset[$ymd] : (self::emptyDayResult(0, $date));
            $divisorRow = isset($divisorResultset[$ymd]) ? $divisorResultset[$ymd] : (self::emptyDayResult(0, $date));
            $weekday    = strtolower($date->format('l'));
            $isOpen     = $openHours[$weekday]['isOpen'];
            $open       = (int) strstr($openHours[$weekday]['open'], ':', true);
            $close      = (int) strstr($openHours[$weekday]['close'], ':', true);
            for ($i = 0; $i < 24; $i++) {
                $hour                                      = (($i < 10) ? '0' : '') . $i;
                $return['breakdown'][$ymd]['hours'][$hour] = [
                    'open'  => $isOpen && $i >= $open && $i <= $close,
                    'total' => self::rate($baseRow['h' . $hour], $divisorRow['h' . $hour])
                ];
            }
            $return['breakdown'][$ymd]['totals']['open']  = self::rate($baseRow['total_open'], $divisorRow['total_open']);
            $return['breakdown'][$ymd]['totals']['close'] = self::rate($baseRow['total_close'], $divisorRow['total_close']);
            $return['breakdown'][$ymd]['totals']['total'] = self::rate($baseRow['total_total'], $divisorRow['total_total']);            
            $openB  += $baseRow['total_open'];
            $openD  += $divisorRow['total_open'];
            $closeB += $baseRow['total_close'];
            $closeD += $divisorRow['total_close'];
            $totalB += $baseRow['total_total'];
            $totalD += $divisorRow['total_total'];
            date_add($date, date_interval_create_from_date_string('1 days'));
        }        
        $return['totals']['open'] += self::rate($openB, $openD);
        $return['totals']['close'] += self::rate($closeB, $closeD);
        $return['totals']['total'] += self::rate($totalB, $totalD);        
        return $return;
    }

    public static function formatAsSum ($startDate, $endDate, $resultset, $openHours)
    {
        $return = self::$baseFormat;
        $date   = new DateTime($startDate);
        $end    = new DateTime($endDate);
        while ($date <= $end) {
            $ymd     = $date->format('Y-m-d');
            $row     = isset($resultset[$ymd]) ? $resultset[$ymd] : (self::emptyDayResult(0, $date));
            $weekday = strtolower($date->format('l'));
            $isOpen  = $openHours[$weekday]['isOpen'];
            $open    = (int) strstr($openHours[$weekday]['open'], ':', true);
            $close   = (int) strstr($openHours[$weekday]['close'], ':', true);
            for ($i = 0; $i < 24; $i++) {
                $hour                                      = (($i < 10) ? '0' : '') . $i;
                $return['breakdown'][$ymd]['hours'][$hour] = [
                    'open'  => $isOpen && $i >= $open && $i <= $close,
                    'total' => $row['h' . $hour]
                ];
            }
            $return['breakdown'][$ymd]['totals']['open']  = $row['total_open'];
            $return['breakdown'][$ymd]['totals']['close'] = $row['total_close'];
            $return['breakdown'][$ymd]['totals']['total'] = $row['total_total'];
            $return['totals']['open'] += $row['total_open'];
            $return['totals']['close'] += $row['total_close'];
            $return['totals']['total'] += $row['total_total'];
            date_add($date, date_interval_create_from_date_string('1 days'));
        }
        return $return;
    }

    public static function nightclubHoursSwitch ($formattedResult, $nightTimezone, $nightclubHours, $nightclubLocation)
    {
        if ($nightclubHours === 'yes') {
            if(!ValidatorComponent::isTimezone($nightclubLocation)){
                switch ($nightclubLocation) {
                    case 'eastcoast_time' :
                    case 'eastcost_time' :
                        $tz = 'America/Detroit';
                        break;
                    case 'pacific_time' :
                        $tz = 'America/Los_Angeles';
                        break;
                    case 'mountain_time' :
                        $tz = 'America/Denver';
                        break;
                    case 'central_time' :
                        $tz = 'America/Chicago';
                        break;
                    case 'eastaustralian_time':
                        $tz = 'Australia/Brisbane';
                        break;
                    default : return $formattedResult;
                }
            } else {
                $tz = $nightclubLocation;
            }
            
            $return = [
                'breakdown' => [],
                'totals'    => $formattedResult['totals']
            ];
            foreach ($formattedResult['breakdown'] as $date => $dayInfo) {
                $return['breakdown'][$date]['totals'] = $dayInfo['totals'];
                foreach ($dayInfo['hours'] as $h => $hourInfo) {
                    $nightDate     = new DateTime("2014-01-01 $h:00:00", new DateTimeZone($nightTimezone));
                    $convertedDate = $nightDate->setTimezone(new DateTimeZone($tz));
                    $return['breakdown'][$date]['hours'][$convertedDate->format('H')] = $hourInfo;
                }
            }
            return $return;
        }
        return $formattedResult;
    }

    public static function rate ($base, $divisor)
    {
        $possibleDivisors   = array_filter([$divisor, max($base, 1)]);
        $nonZeroDivisor     = array_shift($possibleDivisors);
        return min(100, round(($base / $nonZeroDivisor) * 100, 2));
    }

    public static function ratio ($base, $divisor)
    {
        $possibleDivisors   = array_filter([$divisor, 1]);
        $nonZeroDivisor     = array_shift($possibleDivisors);
        return round(($base / $nonZeroDivisor), 2);
    }

}
