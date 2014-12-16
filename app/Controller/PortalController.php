<?php

App::uses('VisitorEvent', 'Model/Portal');
App::uses('PortalTraffic', 'Model/Metrics');
App::uses('Totals', 'Model/Totals');
App::uses('LocationSetting', 'Model/Location');

class PortalController extends AppController
{

    private $device        = [];
    private $timezone      = [];
    private $totals        = [];
    private $portalTraffic = [];
    private $openHours     = [];
    //Avoid infinite recursivity
    private $errorsCount   = 0;

    public function visitorEvent ()
    {
        $upload = $this->request->data('upload');
        $count  = 0;
        $errors = [];
        if (!empty($upload)) {
            $upload = (!is_array($upload)) ? json_decode($upload, true) : $upload;
            $now    = date('Y-m-d H:i:s');
            foreach ($upload as $row) {
                $safeRow = [
                    'serialNumber'  => isset($row['serialNumber']) ? $row['serialNumber'] : 0,
                    'locationID'    => isset($row['locationID']) ? $row['locationID'] : 0,
                    'userID'        => isset($row['userID']) ? $row['userID'] : 0,
                    'entered'       => isset($row['entered']) ? $row['entered'] : 0,
                    'exited'        => isset($row['exited']) ? $row['exited'] : 0,
                    'totalCount'    => isset($row['totalCount']) ? $row['totalCount'] : 0,
                    'date'          => isset($row['date']) ? $row['date'] : $now,  
                ];
                $this->errrorsCount = 0;
                $id                 = $this->insertVisitorEvent($safeRow, $now);
                if (!empty($id)) {
                    $this->updateCache($safeRow);
                    $count++;
                }
                else {
                    $errors[] = $row;
                }
            }
        }
        return new JsonResponse(['body' => ['count' => $count, 'errors' => $errors]]);
    }

    private function insertVisitorEvent ($row, $now)
    {
        try {
            $visitorEvent = new VisitorEvent();
            $visitorEvent->save([
                'device_id'   => isset($row['serialNumber']) ? $row['serialNumber'] : 0,
                'location_id' => isset($row['locationID']) ? $row['locationID'] : 0,
                'user_id'     => isset($row['userID']) ? $row['userID'] : 0,
                'entered'     => isset($row['entered']) ? $row['entered'] : 0,
                'exited'      => isset($row['exited']) ? $row['exited'] : 0,
                'total_count' => isset($row['totalCount']) ? $row['totalCount'] : 0,
                'ts'          => isset($row['date']) ? $row['date'] : $now,
                'ts_creation' => $now,
                'ts_update'   => $now,
            ]);
            return $visitorEvent->id;
        }
        catch (Exception $e) {            
            if ($this->errorsCount < 3) {
                $this->errorsCount++;
                $this->insertVisitorEvent($row, $now);
            }
        }
    }

    private function updateCache ($row)
    {
        $this->setDefaults($row['locationID']);
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $row['date']);
        if (!$date) {
            $date = new DateTime($row['date']);
        }
        $timezone       = new DateTimeZone($this->timezone[$row['locationID']]);
        $date->setTimezone($timezone);
        $rollupsDate    = $date->format('Y-m-d');
        $rollupsHour    = $date->format('H');
        $rollupsWeekday = strtolower($date->format('l'));

        $openHours = $this->openHours[$row['locationID']][$rollupsWeekday];
        $openTime  = DateTime::createFromFormat('Y-m-d H:i:s', $rollupsDate . ' ' . $openHours['open'] . ':00', $timezone);
        $closeTime = DateTime::createFromFormat('Y-m-d H:i:s', $rollupsDate . ' ' . $openHours['close'] . ':59', $timezone);

        if ($openHours['isOpen'] && $date >= $openTime && $date <= $closeTime) {
            $openClose          = 'open';
            $this->errrorsCount = 0;
            $this->updateTotals($row, $rollupsDate);
        }
        else {
            $openClose = 'close';
        }
        $this->errrorsCount = 0;
        $this->updatePortalTraffic($row, $openClose, $rollupsDate, $rollupsHour);
    }

    private function updateTotals ($row, $rollupsDate)
    {
        $totals = $this->getTotals($row['locationID'], $rollupsDate);
        try {
            $totals->incby(['portalTraffic'], $row['totalCount'], $row['locationID'], $rollupsDate);
        }
        catch (Exception $e) {
            CakeLog::debug('ERRORTOTALS:' . $e->getMessage());
            if ($this->errorsCount < 3) {
                $this->errorsCount++;
                $this->totals[$row['locationID']][$rollupsDate] = null;
                $this->updateTotals($row, $rollupsDate);
            }
        }
    }

    private function updatePortalTraffic ($row, $openClose, $rollupsDate, $rollupsHour)
    {
        $portalTraffic = $this->getPortalTraffic($row['locationID'], $rollupsDate);
        try {
            $portalTraffic->incby([
                'h' . $rollupsHour,
                'total_' . $openClose,
                'total_total'
                    ], $row['totalCount'], $row['locationID'], $rollupsDate);
        }
        catch (Exception $e) {            
            if ($this->errorsCount < 3) {
                $this->errorsCount++;
                $this->portalTraffic[$row['locationID']][$rollupsDate] = null;
                $this->updatePortalTraffic($row, $openClose, $rollupsDate, $rollupsHour);
            }
        }
    }

    private function setDefaults ($locationId)
    {
        if (!isset($this->device[$locationId])) {
            $locationSetting              = new LocationSetting();
            $locationSetting->setLocationId($locationId);
            $this->timezone[$locationId]  = $locationSetting->getTimezone();
            $this->openHours[$locationId] = $locationSetting->getOpenHours();
        }
    }

    private function getTotals ($locationId, $date)
    {
        if (!isset($this->totals[$locationId][$date])) {
            $totals = new Totals();
            $total  = $totals->find('first', ['conditions' => ['location_id' => $locationId, 'date' => $date]]);
            if (isset($total['Totals'])) {
                $totals->read(null, $total['Totals']['id']);
            }
            $this->totals[$locationId][$date] = $totals;
        }
        return $this->totals[$locationId][$date];
    }

    private function getPortalTraffic ($locationId, $date)
    {
        if (!isset($this->portalTraffic[$locationId][$date])) {
            $portalTraffic = new PortalTraffic();
            $pt            = $portalTraffic->find('first', ['conditions' => ['location_id' => $locationId, 'date' => $date]]);
            if (isset($pt['PortalTraffic'])) {
                $portalTraffic->read(null, $pt['PortalTraffic']['id']);
            }
            $this->portalTraffic[$locationId][$date] = $portalTraffic;
        }
        return $this->portalTraffic[$locationId][$date];
    }

}
