<?php

require_once __DIR__ . '/Component/CompressedFunctions.php';

App::uses('Model', 'Model');
App::uses('Invoice', 'Model');
App::uses('LocationSetting', 'Model');
App::uses('ValidatorComponent', 'Controller/Component');

class LocationController extends AppController
{

    public $uses = ['Invoice', 'LocationSetting', 'Location'];

    public function highlights ()
    {
        $this->Location->readFromParams($this->params->query);
        $storeId          = $this->LocationSetting->getSettingValue('pos_store_id', $this->Location->id);
        $locationTimezone = $this->LocationSetting->getSettingValue('timezone', $this->Location->id);
        if (empty($storeId)) {
            throw new Exception("Incorrect location_id");
        }
        $p         = $this->params->query;
        $startDate = isset($p['start_date']) ? $p['start_date'] : false;
        $endDate   = isset($p['end_date']) ? $p['end_date'] : false;
        if (!ValidatorComponent::isDate($startDate) || !$startDate) {
            throw new Exception("Start Date must be a valid yyyy-mm-dd date");
        }
        if (!ValidatorComponent::isDate($endDate) || !$endDate) {
            throw new Exception("End Date must be a valid yyyy-mm-dd date");
        }
        $result = array_filter([
            'Biggest Ticket' => $this->Invoice->biggestTicket($storeId, $startDate, $endDate, $locationTimezone),
            'Best Hour'      => $this->Invoice->bestHour($storeId, $startDate, $endDate, $locationTimezone),
            'Best Day'       => $this->Invoice->bestDay($storeId, $startDate, $endDate, $locationTimezone)
        ]);
        return new CakeResponse(['status' => 201, 'body' => json_encode($result), 'type' => 'json']);
    }

}
