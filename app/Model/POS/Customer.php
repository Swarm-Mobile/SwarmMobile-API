
<?php

App::uses('AppModel', 'Model');

class Customer extends AppModel
{

    public $useDbConfig       = 'pos';
    public $useTable          = 'customers';
    public $primaryKey        = 'customer_id';
    public $id                = 'customer_id';
    private $storeId          = null;
    private $searchQuery      = null;
    private $searchJoins      = [
        'invoice_line' => [
            'table'      => 'invoice_lines',
            'alias'      => 'InvoiceLine',
            'type'       => 'INNER',
            'conditions' => [
                'Invoice.invoice_id = InvoiceLine.invoice_id'
            ]
        ],
        'product'      => [
            'table'      => 'products',
            'alias'      => 'Product',
            'type'       => 'INNER',
            'conditions' => [
                'InvoiceLine.ls_product_id = Product.ls_product_id',
            ]
        ],
        'category'     => [
            'table'      => 'categories',
            'alias'      => 'Category',
            'type'       => 'INNER',
            'conditions' => [
                'Category.category_id = Product.category',
            ]
        ]
    ];
    private $joinsToProcess   = [];
    private $havingsToProcess = [];

    private function _filterMinVisits ($minVisits)
    {
        if (!ValidatorComponent::isPositiveInt($minVisits)) {
            throw new InvalidArgumentException(
                SwarmErrorCodes::setError('minVisits must be a positive integer.')
            );
        }
        $this->joinsToProcess[]   = 'invoice_line';
        $this->havingsToProcess[] = 'transactions > ' . (int) $minVisits;
    }

    private function _filterMinAmount ($minAmount)
    {
        if (!ValidatorComponent::isPositiveNumber($minAmount)) {
            throw new InvalidArgumentException(
                SwarmErrorCodes::setError('minAmount must be a positive number.')
            );
        }
        $this->havingsToProcess[] = 'amount > ' . (float) $minAmount;
    }

    private function _filterMinTransactions ($minTransactions)
    {
        if (!ValidatorComponent::isPositiveInt($minTransactions)) {
            throw new InvalidArgumentException(
                SwarmErrorCodes::setError('minTransactions must be a positive integer.')
            );
        }
        $this->joinsToProcess[]   = 'invoice_line';
        $this->havingsToProcess[] = 'transactions > ' . (int) $minTransactions;
    }

    private function _filterBySKU ($sku)
    {
        if (!ValidatorComponent::isSku($sku)) {
            throw new InvalidArgumentException(
                SwarmErrorCodes::setError('sku must be a valid SKU (10-20 characters)')
            );
        }
        $this->joinsToProcess[]                                         = 'invoice_line';
        $this->joinsToProcess[]                                         = 'product';
        $this->searchJoins['product']['conditions']['Product.store_id'] = $this->storeId;
        $this->searchJoins['product']['conditions']['Product.sku']      = $sku;
    }

    private function _filterByCategory ($class)
    {
        $this->joinsToProcess[]                                           = 'invoice_line';
        $this->joinsToProcess[]                                           = 'product';
        $this->joinsToProcess[]                                           = 'category';
        $this->searchJoins['product']['conditions']['Product.store_id']   = $this->storeId;
        $this->searchJoins['category']['conditions']['Category.store_id'] = $this->storeId;
        $this->searchJoins['category']['conditions']['Category.name']     = $class;
    }

    private function _filterByBrand ($brand)
    {
        $this->joinsToProcess[]                                                = 'invoice_line';
        $this->searchJoins['invoice_line']['conditions']['InvoiceLine.family'] = $brand;
    }

    private function _filterHasEmail ($hasEmail)
    {
        if (!ValidatorComponent::isBoolean($hasEmail)) {
            throw new InvalidArgumentException(
                SwarmErrorCodes::setError('hasEmail must be boolean.')
            );
        }
        if ($hasEmail) {
            $this->searchQuery['joins'][0]['conditions']['Invoice.email !='] = [''];
        }
    }

    private function _filterByStartDate ($startDate)
    {
        if (!ValidatorComponent::isDate($startDate)) {
            throw new InvalidArgumentException(
                SwarmErrorCodes::setError('start_date must be a valid yyyy-mm-dd date.')
            );
        }
        $this->searchQuery['joins'][0]['conditions']['Invoice.ts >='] = $startDate . ' 00:00:00';
    }

    private function _filterByEndDate ($endDate)
    {
        if (!ValidatorComponent::isDate($endDate)) {
            throw new InvalidArgumentException(
                SwarmErrorCodes::setError('end_date must be a valid yyyy-mm-dd date.')
            );
        }
        $this->searchQuery['joins'][0]['conditions']['Invoice.ts <='] = $endDate . ' 23:59:59';
    }

    private function _filterOutlet ($outletId)
    {
        if (!ValidatorComponent::isPositiveInt($outletId)) {
            throw new InvalidArgumentException(
                SwarmErrorCodes::setError('outletId must be a positive integer.')
            );
        }
        $this->searchQuery['joins'][0]['conditions']['Invoice.outlet_id'] = (int) $outletId;
    }

    private function _filterRegister ($registerId)
    {
        if (!ValidatorComponent::isPositiveInt($registerId)) {
            throw new InvalidArgumentException(
                SwarmErrorCodes::setError('registerId must be a positive integer.')
            );
        }
        $this->searchQuery['joins'][0]['conditions']['Invoice.register_id'] = (int) $registerId;
    }

    private function addFilters ($filters)
    {
        //We save the current status to recovery it after
        $tmp                    = $this->searchJoins;
        $this->joinsToProcess   = [];
        $this->havingsToProcess = [];
        foreach ($filters as $k => $v) {
            switch ($k) {
                case 'outlet' : $this->_filterOutlet($v);
                    break;
                case 'register' : $this->_filterRegister($v);
                    break;
                case 'visit' : $this->_filterMinVisits($v);
                    break;
                case 'sku' : $this->_filterBySKU($v);
                    break;
                case 'category' : $this->_filterByCategory($v);
                    break;
                case 'hasEmail' : $this->_filterHasEmail($v);
                    break;
                case 'transaction' : $this->_filterMinTransactions($v);
                    break;
                case 'amount' : $this->_filterMinAmount($v);
                    break;
                case 'brand' : $this->_filterByBrand($v);
                    break;
                case 'start_date' : $this->_filterByStartDate($v);
                    break;
                case 'end_date' : $this->_filterByEndDate($v);
                    break;
            }
        }
        $this->joinsToProcess = array_unique($this->joinsToProcess);
        foreach ($this->joinsToProcess as $joinIndex) {
            $this->searchQuery['joins'][] = $this->searchJoins[$joinIndex];
        }
        //Recover the previous status
        $this->searchJoins = $tmp;
        if (!empty($this->havingsToProcess)) {
            $this->searchQuery['group'][count($this->searchQuery['group']) - 1] .= ' HAVING ' . implode(' AND ', $this->havingsToProcess);
        }
    }

    public function search ($storeId, $filters = [], $order = 'last_seen', $limit = 25, $offset = 0, $locationTimezone = 'America/Los_Angeles')
    {
        if (!ValidatorComponent::isPositiveInt($storeId)) {
            throw new InvalidArgumentException(
                SwarmErrorCodes::setError('storeId must be a positive integer.')
            );
        }
        if (!ValidatorComponent::isPositiveInt($limit)) {
            throw new InvalidArgumentException(
                SwarmErrorCodes::setError('limit must be a positive integer.')
            );
        }
        if (!ValidatorComponent::isPositiveInt($offset)) {
            throw new InvalidArgumentException(
                SwarmErrorCodes::setError('offset must be a positive integer.')
            );
        }
        if (!ValidatorComponent::isTimezone($locationTimezone)) {
            throw new InvalidArgumentException(
                SwarmErrorCodes::setError('Incorrect Location Timezone')
            );
        }

        $this->storeId     = $storeId;
        $this->searchQuery = [
            'conditions' => [],
            'fields'     => [
                'Customer.customer_id',
                'Customer.ls_customer_id',
                'Customer.firstname',
                'Customer.lastname',
                'Customer.email',
                'ROUND(COUNT(`Invoice`.`invoice_id`) / (COUNT(Invoice.invoice_id) / COUNT(DISTINCT Invoice.invoice_id))) as transactions',
                'SUM(Invoice.total)/(COUNT(Invoice.invoice_id)/COUNT(DISTINCT Invoice.invoice_id)) as amount',
                'MAX(CONVERT_TZ(Invoice.ts, "GMT", "' . $locationTimezone . '")) as last_seen'
            ],
            'joins'      => [
                [
                    'table'      => 'invoices',
                    'alias'      => 'Invoice',
                    'type'       => 'INNER',
                    'conditions' => [
                        'Invoice.store_id = Customer.store_id',
                        'Invoice.customer_id = Customer.ls_customer_id',
                        'Invoice.completed = 1',
                        'Customer.store_id' => $this->storeId
                    ]
                ]
            ],
            'group'      => ['Customer.customer_id'],
            'order'      => $order . ' DESC',
            'limit'      => $limit,
            'offset'     => $offset,
        ];
        $this->addFilters($filters);               
        return $this->find('all', $this->searchQuery);
    }

}
