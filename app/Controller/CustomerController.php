<?php

require_once __DIR__ . '/Component/CompressedFunctions.php';

App::uses('Model', 'Model');
App::uses('Location', 'Model');
App::uses('Customer', 'Model');
App::uses('ValidatorComponent', 'Controller/Component');

class CustomerController extends AppController
{

    public $uses = ['Customer', 'Location', 'Invoice', 'LocationSetting'];

    public function customer ()
    {
        $this->layout = 'blank';
        $customerId       = $this->request->params['id'];        
        $customer         = $this->Customer->find('first', ['conditions' => ['Customer.customer_id' => $customerId]]);
        try {
            if (empty($customer)) {
                throw new InvalidArgumentException('Incorrect customer_id');
            }            
            $result = [
                'id'              => $customer['Customer']['customer_id'],
                'pos_customer_id' => $customer['Customer']['ls_customer_id'],
                'fullname'        => ucwords(strtolower($customer['Customer']['firstname'] . ' ' . $customer['Customer']['lastname'])),
                'phone'           => $customer['Customer']['phone'],
                'email'           => $customer['Customer']['email'],
                'address'         => ucwords(strtolower($customer['Customer']['address1'] . ' ' . $customer['Customer']['address2'])),
                'city'            => ucwords(strtolower($customer['Customer']['city'])),
                'state'           => $customer['Customer']['state'],
                'country'         => '',
                'transactions'    => []
            ];
            
            $setting = $this->LocationSetting->find('first', 
                [
                    'conditions'=>[
                        'value'=>$customer['Customer']['store_id'],
                        'setting_id'=> settId('pos_store_id')
                    ]
                ]
            );   
            if(empty($setting)){
                throw new InvalidArgumentException('Invalid location_id');                
            }
            $locationId         = $setting['LocationSetting']['location_id'];
            $locationIdRequest  = $this->params->query['location_id'];
            $posStoreIdRequest  = $this->LocationSetting->getSettingValue('pos_store_id', $locationIdRequest);            
            if($customer['Customer']['store_id'] != $posStoreIdRequest){
                throw new InvalidArgumentException('Invalid location_id');
            }
            
            $locationTimezone   = $this->LocationSetting->getSettingValue('timezone', $locationId);
            
            try {
                new DateTimeZone($locationTimezone);
            } catch(Exception $e){
                $locationTimezone = 'America/Los_Angeles';                
            }
            
            $invoices = $this->Invoice->find('all', [
                'fields' => [
                    'CONVERT_TZ(ts, "GMT", "' . $locationTimezone . '") ts',
                    'Invoice.total',
                ],
                'conditions' => [
                    'Invoice.customer_id' => $customer['Customer']['ls_customer_id'],
                    'Invoice.store_id' => $customer['Customer']['store_id'],
                    'Invoice.completed' => 1                   
                ],
                'order' => 'Invoice.ts DESC'
            ]);          
            if(!empty($invoices)){
                foreach ($invoices as $invoice) {
                    $transaction = [
                        'date'  => $invoice[0]['ts'],
                        'total' => $invoice['Invoice']['total'],
                        'items' => 0,
                        'lines' => []
                    ];
                    foreach ($invoice['InvoiceLine'] as $line) {
                        $transaction['lines'][] = [
                            'description' => ucwords(strtolower($line['description'])),
                            'quantity'    => $line['quantity'],
                            'price'       => $line['price']
                        ];
                        $transaction['items'] += $line['quantity'];
                    }
                    $result['transactions'][] = $transaction;
                }
            }
        }
        catch (InvalidArgumentException $e) {
            $result = ['error' => $e->getMessage()];
        }
        $this->set('result',$result);
        $this->render('/API/json');
    }

    public function customers ()
    {
        $this->layout = 'blank';
        $locationId   = $this->params->query['location_id'];
        try {
            $this->Location->read(null, $locationId);            
            if (empty($this->Location->data)) {
                throw new InvalidArgumentException("Incorrect location_id");
            }
            
            $storeId = settVal('pos_store_id', $this->Location->data['Setting']);
            if (empty($storeId)) {
                throw new InvalidArgumentException("Incorrect location_id");
            }         
            
            $locationTimezone   = $this->LocationSetting->getSettingValue('timezone', $locationId);
            
            try {
                new DateTimeZone($locationTimezone);
            } catch(Exception $e){
                $locationTimezone = 'America/Los_Angeles';                
            }
           
            $p         = $this->params->query;     
            $order  = isset($p['order']) ? $p['order'] : 'last_seen';
            $limit  = isset($p['limit']) ? $p['limit'] : 25;
            $page   = isset($p['page'])  ? $p['page']  : 1;
            $offset = ($page > 1) ? $page * $limit : 0;

            $filters   = array_filter([                               
                'visit'       => isset($p['minVisits'])       ? $p['minVisits']       : false,
                'sku'         => isset($p['sku'])             ? $p['sku']             : false,
                'category'    => isset($p['category'])        ? $p['category']        : false,
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
            $customers = $this->Customer->search($storeId, $filters, $order, $limit, $offset, $locationTimezone);
            if (!empty($customers)) {
                foreach ($customers as $customer) {
                    $result[] = [
                        'id'              => $customer['Customer']['customer_id'],
                        'pos_customer_id' => $customer['Customer']['ls_customer_id'],
                        'fullname'        => ucwords(strtolower(trim($customer['Customer']['firstname'] . ' ' . $customer['Customer']['lastname']))),
                        'email'           => $customer['Customer']['email'],
                        'transactions'    => $customer[0]['transactions'],
                        'amount'          => round($customer[0]['amount'],2),
                        'last_seen'       => $customer[0]['last_seen']
                    ];
                }
            }
        }        
        catch (InvalidArgumentException $e) {
            $result = ['error' => $e->getMessage()];
        }
        $this->set('result',$result);
        $this->render('/API/json');
    }

    public function beforeFilter ()
    {
        $this->Auth->allow('customer', 'customers');
    }

}
