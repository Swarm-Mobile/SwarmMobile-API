<?php

require_once __DIR__ . '/Component/CompressedFunctions.php';

App::uses('Model', 'Model');
App::uses('Location', 'Model');
App::uses('Customer', 'Model');
App::uses('ValidatorComponent', 'Controller/Component');

class CustomerController extends AppController
{

    public $uses = ['Customer', 'Location', 'Invoice'];

    public function customer ()
    {
        $this->autoRender = false;
        $customerId       = $this->request->params['id'];
        $customer         = $this->Customer->find('first', ['conditions' => ['Customer.customer_id' => $customerId]]);
        try {
            if (empty($customer)) {
                throw new InvalidArgumentException('Incorrect customer_id');
            }            
            $result = [
                'id'              => $customer['Customer']['customer_id'],
                'pos_customer_id' => $customer['Customer']['ls_customer_id'],
                'fullname'        => $customer['Customer']['firstname'] . ' ' . $customer['Customer']['lastname'],
                'phone'           => $customer['Customer']['phone'],
                'email'           => $customer['Customer']['email'],
                'address'         => $customer['Customer']['address1'] . ' ' . $customer['Customer']['address2'],
                'city'            => $customer['Customer']['city'],
                'state'           => $customer['Customer']['state'],
                'country'         => '',
                'transactions'    => []
            ];
            $invoices         = $this->Invoice->find('all', [
                'conditions' => [
                    'Invoice.customer_id' => $customer['Customer']['ls_customer_id'],
                    'Invoice.store_id' => $customer['Customer']['store_id'],
                    'Invoice.completed' => 1
                ]
            ]);            
            foreach ($invoices as $invoice) {
                $transaction = [
                    'date'  => $invoice['Invoice']['ts'],
                    'total' => $invoice['Invoice']['total'],
                    'items' => 0,
                    'lines' => []
                ];
                foreach ($invoice['InvoiceLine'] as $line) {
                    $transaction['lines'][] = [
                        'description' => $line['description'],
                        'quantity'    => $line['quantity'],
                        'price'       => $line['price']
                    ];
                    $transaction['items'] += $line['quantity'];
                }
                $result['transactions'][] = $transaction;
            }
        }
        catch (InvalidArgumentException $e) {
            $result = ['error' => $e->getMessage()];
        }
        echo json_encode($result);
    }

    public function customers ()
    {
        $this->autoRender = false;
        $locationId       = $this->params->query['location_id'];
        try {
            if (!ValidatorComponent::isPositiveInt($locationId)) {
                throw new InvalidArgumentException('location_id must be a positive integer.');
            }
            $this->Location->read(null, $locationId);
            if (empty($this->Location->data)) {
                throw new InvalidArgumentException("Incorrect location_id");
            }
            $storeId = settVal('pos_store_id', $this->Location->data['Setting']);

            $order  = coalesce($this->params['order'], 'last_seen');
            $limit  = coalesce($this->params['limit'], 25);
            $page   = coalesce($this->params['page'], 1);
            $offset = ($page > 1) ? $page * $limit : 0;

            $p         = $this->params->query;
            $filters   = array_filter([
                'visit'       => isset($p['minVisits'])       ? $p['minVisits']       : false,
                'sku'         => isset($p['sku'])             ? $p['sku']             : false,
                'class'       => isset($p['class'])           ? $p['class']           : false,
                'hasEmail'    => isset($p['hasEmail'])        ? $p['hasEmail']        : false,
                'transaction' => isset($p['minTransactions']) ? $p['minTransactions'] : false,
                'amount'      => isset($p['minAmount'])       ? $p['minAmount']       : false,
                'brand'       => isset($p['brand'])           ? $p['brand']           : false,
                'start_date'  => isset($p['start_date'])      ? $p['start_date']      : false,
                'end_date'    => isset($p['end_date'])        ? $p['end_date']        : false,
                'outlet'      => coalesce(settVal('outlet_filter', $this->Location->data['Setting']), false),
                'register'    => coalesce(settVal('register_filter', $this->Location->data['Setting']), false),
            ]);
            $result    = [];
            $customers = $this->Customer->search($storeId, $filters, $order, $limit, $offset);
            if (!empty($customers)) {
                foreach ($customers as $customer) {
                    $result[] = [
                        'id'              => $customer['Customer']['customer_id'],
                        'pos_customer_id' => $customer['Customer']['ls_customer_id'],
                        'fullname'        => trim($customer['Customer']['firstname'] . ' ' . $customer['Customer']['lastname']),
                        'email'           => $customer['Customer']['email'],
                        'transactions'    => $customer[0]['transactions'],
                        'amount'          => $customer[0]['amount'],
                        'last_seen'       => $customer[0]['last_seen']
                    ];
                }
            }
        }
        catch (InvalidArgumentException $e) {
            $result = ['error' => $e->getMessage()];
        }
        echo json_encode($result);
                        foreach($this->Customer->getDataSource()->getLog()['log'] as $query){
            echo nl2br($query['query']);
        }
    }

    public function beforeFilter ()
    {
        $this->Auth->allow('customer', 'customers');
    }

}
