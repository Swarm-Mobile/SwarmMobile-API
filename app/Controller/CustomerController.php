<?php

App::uses('Model', 'Model');
App::uses('Location', 'Model/Location');
App::uses('LocationSetting', 'Model/Location');
App::uses('Invoice', 'Model/POS');
App::uses('Customer', 'Model/POS');

class CustomerController extends AppController
{

    protected $customer;
    protected $location;
    protected $invoice;
    protected $invoiceLine;
    protected $locationSetting;

    public function getCustomer ()
    {
        if (empty($this->customer)) {
            App::uses('Customer', 'Model/POS');
            $this->customer = new Customer();
        }
        return $this->customer;
    }

    public function getLocation ()
    {
        if (empty($this->location)) {
            App::uses('Location', 'Model/Location');
            $this->location = new Location();
        }
        return $this->location;
    }

    public function getInvoice ()
    {
        if (empty($this->invoice)) {
            App::uses('Invoice', 'Model/POS');
            $this->invoice = new Invoice();
        }
        return $this->invoice;
    }

    public function getInvoiceLine ()
    {
        if (empty($this->invoiceLine)) {
            App::uses('InvoiceLine', 'Model/POS');
            $this->invoiceLine = new InvoiceLine();
        }
        return $this->invoiceLine;
    }

    public function getLocationSetting ()
    {
        if (empty($this->locationSetting)) {
            App::uses('LocationSetting', 'Model/Location');
            $this->locationSetting = new LocationSetting();
        }
        return $this->locationSetting;
    }

    public function setCustomer (Customer $customer)
    {
        $this->customer = $customer;
        return $this;
    }

    public function setLocation (Location $location)
    {
        $this->location = $location;
        return $this;
    }

    public function setInvoice (Invoice $invoice)
    {
        $this->invoice = $invoice;
        return $this;
    }

    public function setInvoiceLine (InvoiceLine $invoiceLine)
    {
        $this->invoiceLine = $invoiceLine;
        return $this;
    }

    public function setLocationSetting (LocationSetting $locationSetting)
    {
        $this->locationSetting = $locationSetting;
        return $this;
    }

    public function customer ()
    {
        $errors = AppModel::validationErrors(['customer_id', 'location_id'], $this->request->query);
        if (!empty($errors)) {
            throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($errors));
        }
        
        $locationId      = $this->params->query['location_id'];
        $locationSetting = $this->getLocationSetting();
        $locationSetting->setLocationId($locationId);

        $customer = $this->getCustomer();
        $customer->read(null, $this->params->query('customer_id'));
        $c        = $customer->data['Customer'];
        if (empty($c) || empty($c['store_id']) || !$this->_customerCheckLocation($locationId, $c['store_id'])) {
            throw new Swarm\UnprocessableEntityException(SwarmErrorCodes::CUSTOMER_CUSTOMER_INVALID_CUSTOMER);
        }

        $customerResult = $this->_customerGetResultStruct($customer->data['Customer']);
        $invoices       = $this->_customerGetInvoices($customer->data['Customer'], $locationSetting->getTimezone());
        $result         = $this->_customerProcessInvoices($customerResult, $invoices);
        return new JsonResponse(['body' => $result]);
    }

    public function customers ()
    {
        $errors = AppModel::validationErrors(['location_id'], $this->request->query);
        if (!empty($errors)) {
            throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($errors));
        }
        
        $location = $this->getLocation();
        $location->read(null, $this->params->query['location_id']);

        $locationSetting = $this->getLocationSetting();
        $locationSetting->setLocationId($this->params->query['location_id']);
        $storeId         = $locationSetting->getSettingValue(LocationSetting::POS_STORE_ID);
        if (empty($storeId)) {
            throw new Swarm\UnprocessableEntityException(SwarmErrorCodes::CUSTOMER_CUSTOMERS_STORE_NOTFOUND);
        }

        $p                = $this->params->query;
        $order            = isset($p['order']) ? $p['order'] : 'last_seen';
        $limit            = isset($p['limit']) ? $p['limit'] : 25;
        $page             = isset($p['page']) ? $p['page'] : 1;
        $offset           = ($page > 1) ? $page * $limit : 0;
        $filters          = $this->_customersGetFilters($this->params->query);
        $locationTimezone = $locationSetting->getTimezone();

        $customerModel = $this->getCustomer();
        $customers     = $customerModel->search($storeId, $filters, $order, $limit, $offset, $locationTimezone);
        return new JsonResponse(['body' => $this->_customersProcessCustomers($customers)]);
    }

    private function _customerGetResultStruct ($customer)
    {
        $c                 = $customer;
        $possibleFullnames = array_filter([ucwords(strtolower($c['firstname'] . ' ' . $c['lastname'])), '']);
        $possiblePhones    = array_filter([$c['phone'], ' ']);
        $possibleEmails    = array_filter([$c['email'], ' ']);
        $possibleAddresses = array_filter([ucwords(strtolower($c['address1'] . ' ' . $c['address2'])), '']);
        $possibleCities    = array_filter([ucwords(strtolower($c['city'])), '']);
        $possibleStates    = array_filter([$c['state'], ' ']);
        $result            = [
            'id'              => $c['customer_id'],
            'pos_customer_id' => $c['ls_customer_id'],
            'fullname'        => array_shift($possibleFullnames),
            'phone'           => array_shift($possiblePhones),
            'email'           => array_shift($possibleEmails),
            'address'         => array_shift($possibleAddresses),
            'city'            => array_shift($possibleCities),
            'state'           => array_shift($possibleStates),
            'country'         => '',
            'transactions'    => []
        ];
        return $result;
    }

    private function _customerGetInvoices ($customer, $locationTimezone)
    {
        $invoiceModel = $this->getInvoice();
        return $invoiceModel->find('all', [
                    'fields'     => [
                        'Invoice.invoice_id',
                        'CONVERT_TZ(ts, "GMT", "' . $locationTimezone . '") ts',
                        'Invoice.total',
                    ],
                    'conditions' => [
                        'Invoice.customer_id' => $customer['ls_customer_id'],
                        'Invoice.store_id'    => $customer['store_id'],
                        'Invoice.completed'   => 1
                    ],
                    'order'      => 'Invoice.ts DESC'
        ]);
    }

    private function _customerProcessInvoices ($result, $invoices = [])
    {
        if (!empty($invoices)) {
            $invoiceLineModel = $this->getInvoiceLine();
            foreach ($invoices as $invoice) {
                $transaction = [
                    'date'  => $invoice[0]['ts'],
                    'total' => $invoice['Invoice']['total'],
                    'items' => 0,
                    'lines' => []
                ];
                $lines       = $invoiceLineModel->find('all', [
                    'conditions' => ['invoice_id' => $invoice['Invoice']['invoice_id']]
                ]);
                foreach ($lines as $line) {
                    $transaction['lines'][] = [
                        'description' => ucwords(strtolower($line['InvoiceLine']['description'])),
                        'quantity'    => $line['InvoiceLine']['quantity'],
                        'price'       => $line['InvoiceLine']['price']
                    ];
                    $transaction['items'] += $line['InvoiceLine']['quantity'];
                }
                $result['transactions'][] = $transaction;
            }
        }
        return $result;
    }

    private function _customerCheckLocation ($location_id, $store_id)
    {
        $locationSetting = $this->getLocationSetting();
        $setting         = $locationSetting->find('first', [
            'conditions' => [
                'value'       => $store_id,
                'setting_id'  => LocationSetting::POS_STORE_ID,
                'location_id' => $location_id
            ]
        ]);
        return !empty($setting);
    }

    private function _customersGetFilters ($params)
    {
        $p                 = $params;
        $locationSetting   = $this->getLocationSetting();
        $possibleOutlets   = array_filter([$locationSetting->getSettingValue(LocationSetting::OUTLET_FILTER), false]);
        $possibleRegisters = array_filter([$locationSetting->getSettingValue(LocationSetting::REGISTER_FILTER), false]);
        return array_filter([
            'visit'       => isset($p['minVisits']) ? $p['minVisits'] : false,
            'sku'         => isset($p['sku']) ? $p['sku'] : false,
            'category'    => isset($p['category']) ? $p['category'] : false,
            'hasEmail'    => isset($p['hasEmail']) ? $p['hasEmail'] : false,
            'transaction' => isset($p['minTransactions']) ? $p['minTransactions'] : false,
            'amount'      => isset($p['minAmount']) ? $p['minAmount'] : false,
            'brand'       => isset($p['brand']) ? $p['brand'] : false,
            'start_date'  => isset($p['start_date']) ? $p['start_date'] : false,
            'end_date'    => isset($p['end_date']) ? $p['end_date'] : false,
            'outlet'      => array_shift($possibleOutlets),
            'register'    => array_shift($possibleRegisters),
        ]);
    }

    private function _customersProcessCustomers ($customers)
    {
        $result = [];
        if (!empty($customers)) {
            foreach ($customers as $customer) {
                $result[] = [
                    'id'              => $customer['Customer']['customer_id'],
                    'pos_customer_id' => $customer['Customer']['ls_customer_id'],
                    'fullname'        => ucwords(strtolower(trim($customer['Customer']['firstname'] . ' ' . $customer['Customer']['lastname']))),
                    'email'           => $customer['Customer']['email'],
                    'transactions'    => $customer[0]['transactions'],
                    'amount'          => round($customer[0]['amount'], 2),
                    'last_seen'       => $customer[0]['last_seen']
                ];
            }
        }
        return $result;
    }

}
