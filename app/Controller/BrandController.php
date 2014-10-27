<?php

require_once __DIR__ . '/Component/CompressedFunctions.php';

App::uses('Model', 'Model');
App::uses('Invoice', 'Model');
App::uses('Location', 'Model');

class BrandController extends AppController
{

    public $uses = ['Location', 'InvoiceLine'];

    public function brands ()
    {
        $this->Location->readFromParams($this->params->query, 1);
        $storeId = settVal('pos_store_id', $this->Location->data['Setting']);
        if (empty($storeId)) {
            throw new Exception("Incorrect location_id");
        }
        $brands = $this->InvoiceLine->find('all', [
            'fields'     => ['DISTINCT InvoiceLine.family'],
            'conditions' => [
                'InvoiceLine.store_id'  => $storeId,
                'InvoiceLine.family !=' => ['']
            ]
        ]);
        $result = [];
        if (!empty($brands)) {
            foreach ($brands as $brand) {
                $result[] = ucwords(strtolower($brand['InvoiceLine']['family']));
            }
        }
        $this->set('result', $result);
        $this->render('/API/json');
    }

}
