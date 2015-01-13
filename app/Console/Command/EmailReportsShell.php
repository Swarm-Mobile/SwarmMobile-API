<?php

require_once(__DIR__ . '/../../Controller/AppController.php');
require_once(__DIR__ . '/../../Controller/ReportController.php');
require_once(__DIR__ . '/../../Controller/MetricController.php');
require_once(__DIR__ . '/../../Controller/Component/TimeComponent.php');
require_once(__DIR__ . '/../../Controller/Component/CssToInlineStyles.php');
require_once(__DIR__ . '/../../Controller/Component/MetricFormatComponent.php');

App::uses('AppShell', 'Console/Command');
App::uses('Model', 'Model');
App::uses('Location', 'Model/Location/');
App::uses('CakeEmail', 'Network/Email');

class EmailReportsShell extends AppShell {

    public function main()
    {
        $this->out("Processing Location :" . $this->params['location']);
        $this->setEnvironment();
        $range = $this->params['range'];
        $range = empty($range) ? 'day' : $range;
        $metricController = new MetricController(new CakeRequest(), new CakeResponse());
        $reportController = new ReportController(new CakeRequest(), new CakeResponse(), $metricController);
        $date = date('Y-m-d');
        switch ($range) {
            case 'day':
                list($curRangeStart, $curRangeEnd)   = TimeComponent::previousDayRange($date, 1);
                list($prevRangeStart, $prevRangeEnd) = TimeComponent::previousDayRange($date, 8);
                $field = 'daily';
                break;
            case 'week':
                list($curRangeStart, $curRangeEnd)   = TimeComponent::previousWeekRange($date);
                list($prevRangeStart, $prevRangeEnd) = TimeComponent::previousWeekRange($curRangeStart);
                $field = 'weekly';
                break;
            case 'month':
                list($curRangeStart, $curRangeEnd)   = TimeComponent::previousMonthRange($date);
                list($prevRangeStart, $prevRangeEnd) = TimeComponent::previousMonthRange($curRangeStart);
                $field = 'monthly';
                break;
        }
        
        $oModel = new Model(false, 'user_location_report', 'backstage');
        $filter = $this->params['location'] != 'all' ? ' AND l.id = ' . $this->params['location'] . ' ' : ' ';
        $sSQL = <<<SQL
SELECT 
    ulr.location_id,
    ulr.zero_highlights,
    l.name,
    u.email
FROM user_location_report ulr
INNER JOIN location l
    ON l.id = ulr.location_id
    AND $field = 1
    $filter
INNER JOIN user u
    ON u.id = ulr.user_id
SQL;
        $oDb = $oModel->getDataSource();
        $aRes = $oDb->fetchAll($sSQL);
        $result = array();
        foreach ($aRes as $oRow) {
            $result[$oRow['ulr']['location_id']]['name'] = $oRow['l']['name'];
            $result[$oRow['ulr']['location_id']]['zero_highlights'] = $oRow['ulr']['zero_highlights'];
            $result[$oRow['ulr']['location_id']]['emails'][] = $oRow['u']['email'];
        }
        foreach ($result as $location_id => $values) {
            $msg = $reportController->metricReport(
                    $location_id, $values['zero_highlights'], $values['name'], $curRangeStart, $curRangeEnd, $prevRangeStart, $prevRangeEnd, $field
            );
            if ($msg) {
                if ($this->params['location'] == 'all') {
                    foreach ($values['emails'] as $email) {
                        if (!empty($email)) {
                            $sender = new CakeEmail('smtp');
                            $sender->to($email)
                                    ->subject('Swarm Metrics')
                                    ->emailFormat("html")
                                    ->send($msg);
                        }
                    }
                    $sender = new CakeEmail('smtp');
                    $sender->to('reports@swarm-mobile.com')
                            ->subject('Swarm Metrics')
                            ->emailFormat("html")
                            ->send($msg);
                } else {
                    $sender = new CakeEmail('smtp');
                    $sender->to('dev@swarm-mobile.com')
                            ->subject('Swarm Metrics')
                            ->emailFormat("html")
                            ->send($msg);
                }
            }
        }
    }

    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->addOption('range', array(
            'short' => 'r',
            'default' => 'month',
            'help' => "day - week - month"
        ));
        $parser->addOption('location', array(
            'short' => 'l',
            'default' => 'all',
            'help' => "For test a location (send the email to dev@swarm-mobile.com)"
        ));
        return $parser;
    }

}