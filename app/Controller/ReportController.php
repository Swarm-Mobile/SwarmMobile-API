<?php

App::uses('TimeComponent', 'Controller/Component');
App::uses('Location', 'Model/Location');
App::uses('LocationSetting', 'Model/Location');
App::uses('Walkbys',  'Model/Metrics');
App::uses('FootTraffic', 'Model/Metrics');
App::uses('Revenue', 'Model/Metrics');
App::uses('Returning', 'Model/Metrics');
require_once __DIR__ . DS . 'Component' . DS . 'MetricFormatComponent.php';
require_once(__DIR__ . '/Component/CssToInlineStyles.php');

class ReportController extends AppController
{
    public $metricController;
    public $helpers = ['MetricFormat'];
    public function __construct($request, $response, $metricController)
    {
        parent::__construct($request, $response);
        $this->metricController = $metricController;
    }
    
    public function metricReport( $locationId, $zero_highlights, $location_name, 
        $cur_range_start, $cur_range_end, $prev_range_start, $prev_range_end, $range
    )
    {
        $day_open_label = strtoupper((TimeComponent::getDayString($cur_range_start)) . '_OPEN');
        $oLocation = new Location();
        $oLocation = $oLocation->find('first', ['conditions'=>['Location.id'=>$locationId]]);
        $this->set('oLocation',$oLocation);
        $locationSetting = new LocationSetting();
        $locationSetting->create(['location_id' => $locationId], true);
        $day_open          = $locationSetting->getSettingValue(constant('LocationSetting::' . $day_open_label));
        if($day_open == '0') {
            return false;
        }
        $date = new DateTime($cur_range_start);
        $curRangeStartFormat = $date->format('F jS');
        $date = new DateTime($cur_range_end);
        $curRangeEndFormat = $date->format('F jS');
        $date = new DateTime($prev_range_start);
        $prevRangeStartFormat = $date->format('F jS');
        $date = new DateTime($prev_range_end);
        $prevRangeEndFormat = $date->format('F jS');

        $this->set('toName', $location_name);

        if($curRangeStartFormat != $curRangeEndFormat){
            $this->set('curRange', $curRangeStartFormat.' - '.$curRangeEndFormat);
            $this->set('prevRange', $prevRangeStartFormat.' - '.$prevRangeEndFormat);
        } else {
            $this->set('curRange', $curRangeStartFormat);
            $this->set('prevRange', $prevRangeStartFormat);
        }
        $this->set('reportType', ucfirst($range));
        $this->set('zeroHighlights', $zero_highlights);
        
        $curRangeParams = array(
            'location_id'     => $locationId,
            'start_date'    => $cur_range_start,
            'end_date'      => $cur_range_end,
        );
        $prevRangeParams = array(
            'location_id'     => $locationId,
            'start_date'    => $prev_range_start,
            'end_date'      => $prev_range_end
        );
        $metrics = [
            'walkbys'        => ['Walkbys'          , 'People Walking By Your Store'        , 'num'],
            'footTraffic'    => ['Foot Traffic'         , 'People Entering Your Store'          , 'num'],
            'transactions'   => ['Transactions'     , 'Purchases in Your Store'             , 'num'],
            'conversionRate' => ['Conversion Rate'  , 'Transactions / Total Shoppers'       , 'percentage'],
            'dwell'          => ['Dwell Time'       , 'Avg. Time Spent In Store'            , 'time'],
            'returning'      => ['Return Shoppers' , 'Customers Who Have Been Here Before' , 'num']
        ];

        $aResults = [];
        $request = new CakeRequest('');
        $this->metricController->request = $request;
        foreach ($metrics as $metric => $params) {
            $request->query =  $curRangeParams;
            $aResults[] = json_decode($this->metricController->$metric());
            
            $request->query = $prevRangeParams;
            $aResults[] = json_decode($this->metricController->$metric());
        }
        $request->query = $curRangeParams;
        $aResults[] = json_decode($this->metricController->revenue());
        $i = 0;
        //METRICS
        foreach ($metrics as $metric => $params) {
            $curResult = $aResults[$i];
            $prevResult = $aResults[$i + 1];
            if($metric == 'footTraffic' || $metric == 'conversionRate' || $metric == 'returning') {
                $curTotal = $curResult->data->totals->total;
                $prevTotal = $prevResult->data->totals->total;
            } else {
                $curTotal = $curResult->data->totals->open;
                $prevTotal = $prevResult->data->totals->open;
            }
            //Prevent send empty reports
            if($metric == 'walkbys' && $curTotal == 0 && $prevTotal == 0){
                return false;
            }
            
            $this->set($metric . '_value', $curTotal);
            $this->set($metric . '_title', $params[0]);
            $this->set($metric . '_description', $params[1]);
            $percentage = ($prevTotal == 0)?0:round($curTotal / $prevTotal, 2);
            $change     = ($percentage > 1) ? 'increase' : 'decrease';
            $percentage = (($percentage > 1) ? $percentage - 1 : 1 - $percentage) * 100;
            $this->set($metric . '_percentage', $percentage);
            $this->set($metric . '_change', $change);
            $this->set($metric . '_dataType', $params[2]);
            $i+=2;
        }
        $metrics = [
            'footTraffic'    => [2  , 'Busiest'                 , 'Shoppers'    , 'num'],
            'revenue'        => [12 , 'Highest Revenue'         , ''            , 'currency'],
            'conversionRate' => [6  , 'Highest Conversion Rate' , ''            , 'percentage'],
            'transactions'   => [4  , 'Highest Transactions'    , 'Transactions', 'num']
        ];
        
        $i = 0;
        //HIGHLIGHTS
        foreach ($metrics as $metric => $params) {
            foreach (['hour', 'day'] as $range) {
                switch ($range) {
                    case 'day':
                        $to_print = array('', -1);
                        foreach ($aResults[$params[0]]->data->breakdown as $day => $values) {
                            if ($metric == 'footTraffic' || $metric == 'conversionRate') {
                                if ($to_print[1] < $values->totals->total) {
                                    $to_print = array($day, $values->totals->total);
                                }
                            } else {
                                if ($to_print[1] < $values->totals->open) {
                                    $to_print = array($day, $values->totals->open);
                                }
                            }
                        }
                        break;
                    case 'hour':
                        $to_print = array('', -1);
                        foreach ($aResults[$params[0]]->data->breakdown as $day => $values) {
                            foreach ($values->hours as $hour => $openTotal) {
                                if ($to_print[1] < $openTotal->total) {
                                    $to_print = array($day . ' ' . $hour . ':00:00', $openTotal->total);
                                }
                            }
                        }
                        break;
                }
                $date = new DateTime($to_print[0]);
                $dateFormatted = $date->format('F jS' . (($range == 'hour') ? ' H:i' : ''));
                $this->set('highest_' . $metric . '_' . $range . '_range', $dateFormatted);
                $this->set('highest_' . $metric . '_' . $range . '_title', $params[1] . ' ' . ucfirst($range));
                $this->set('highest_' . $metric . '_' . $range . '_value', $to_print[1]);
                $this->set('highest_' . $metric . '_' . $range . '_suffix', $params[2]);
                $this->set('highest_' . $metric . '_' . $range . '_dataType', $params[3]);
            }
        }
        $response = $this->render('/Email/metrics_report', 'blank');
        $html = $response->body();
        $css = file_get_contents(__DIR__ . '/../webroot/css/emails/metrics_report.css');
        $cssToInlineStyles = new TijsVerkoyen\CssToInlineStyles\CssToInlineStyles();
        $cssToInlineStyles->setHTML($html);
        $cssToInlineStyles->setCSS($css);
        return $cssToInlineStyles->convert();
    }

    public function getFootTraffic ($locationSettingModel)
    {
        $device = 'portal';
        $locationSettingModel->setLocationId($data['location_id']);
        $device          = $locationSettingModel->getSettingValue(LocationSetting::FOOTTRAFFIC_DEFAULT_DEVICE);
        $device          = (empty($device)) ? 'portal' : $device;
        $class             = ucfirst($device) . 'Traffic';
        App::uses($class, 'Model/Metrics');
        $footTraffic = new $class();
        return $footTraffic;
    }
}