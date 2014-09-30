<?php
require_once __DIR__ . '/Component/CompressedFunctions.php';

App::uses('Model', 'Model');
App::uses('Invoice', 'Model');
App::uses('Location', 'Model');
App::uses('ValidatorComponent', 'Controller/Component');

class LocationController extends AppController
{

    public $uses = ['Invoice', 'Location'];

    public function highlights ()
    {
        $this->layout = 'blank';
        $locationId   = $this->request->params['location_id'];
        try {
            $this->Location->read(null, $locationId);
            if (empty($this->Location->data)) {
                throw new InvalidArgumentException("Incorrect location_id");
            }
            $storeId = settVal('pos_store_id', $this->Location->data['Setting']);
            if (empty($storeId)) {
                throw new InvalidArgumentException("Incorrect location_id");
            }
            $p         = $this->params->query;
            $startDate = isset($p['start_date']) ? $p['start_date'] : false;
            $endDate   = isset($p['end_date']) ? $p['end_date'] : false;
            if (!ValidatorComponent::isDate($startDate) || !$startDate) {
                throw new InvalidArgumentException("Start Date must be a valid yyyy-mm-dd date");
            }
            if (!ValidatorComponent::isDate($endDate) || !$endDate) {
                throw new InvalidArgumentException("End Date must be a valid yyyy-mm-dd date");
            }            
            $result = array_filter([
                'Biggest Ticket' => $this->Invoice->biggestTicket($storeId, $startDate, $endDate),
                'Best Hour'      => $this->Invoice->bestHour($storeId, $startDate, $endDate),
                'Best Day'       => $this->Invoice->bestDay($storeId, $startDate, $endDate)
            ]);
        }
        catch (InvalidArgumentException $e) {
            $result = ['error' => $e->getMessage()];
        }
        $this->set('result', $result);
        $this->render('/API/json');
    }

    public function beforeFilter ()
    {
        $this->Auth->allow('highlights');
    }

}
