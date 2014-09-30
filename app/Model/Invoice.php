<?php

class Invoice extends AppModel
{

    public $useDbConfig = 'pos';
    public $useTable    = 'invoices';
    public $primaryKey  = 'invoice_id';
    public $id          = 'invoice_id';
    public $hasMany     = [
        'InvoiceLine' => [
            'className'  => 'InvoiceLine',
            'foreignKey' => 'invoice_id',
            'type'       => 'INNER'
        ]
    ];

    public function biggestTicket ($storeId = null, $startDate = null, $endDate = null)
    {
        $conditions = [];
        if (ValidatorComponent::isPositiveInt($storeId)) {
            $conditions['Invoice.store_id'] = $storeId;
        }
        if (ValidatorComponent::isDate($startDate)) {
            $conditions['Invoice.ts >='] = $startDate . ' 00:00:00';
        }
        if (ValidatorComponent::isDate($endDate)) {
            $conditions['Invoice.ts <='] = $endDate . ' 23:59:59';
        }
        $this->recursive = -1;
        $result          = $this->find('first', [
            'fields'     => [
                'DATE(ts) as date',
                'total as amount'
            ],
            'conditions' => $conditions
        ]);
        if (!empty($result)) {
            return [
                'date'   => $result[0]['date'],
                'amount' => $result['Invoice']['amount']
            ];
        }
        return false;
    }

    public function bestHour ($storeId = null, $startDate = null, $endDate = null)
    {
        $conditions = [];
        if (ValidatorComponent::isPositiveInt($storeId)) {
            $conditions['Invoice.store_id'] = $storeId;
        }
        if (ValidatorComponent::isDate($startDate)) {
            $conditions['Invoice.ts >='] = $startDate . ' 00:00:00';
        }
        if (ValidatorComponent::isDate($endDate)) {
            $conditions['Invoice.ts <='] = $endDate . ' 23:59:59';
        }
        $result = $this->find('first', [
            'fields'     => [
                "CONCAT(DATE(ts),' ',HOUR(ts),':00:00') as date",
                'SUM(total) as amount'
            ],
            'conditions' => $conditions,
            'group'      => ['DATE(Invoice.ts)', 'HOUR(Invoice.ts)']
        ]);        
        return (!empty($result))?$result[0]:false;
    }

    public function bestDay ($storeId = null, $startDate = null, $endDate = null)
    {
        $conditions = [];
        if (ValidatorComponent::isPositiveInt($storeId)) {
            $conditions['Invoice.store_id'] = $storeId;
        }
        if (ValidatorComponent::isDate($startDate)) {
            $conditions['Invoice.ts >='] = $startDate . ' 00:00:00';
        }
        if (ValidatorComponent::isDate($endDate)) {
            $conditions['Invoice.ts <='] = $endDate . ' 23:59:59';
        }
        $result = $this->find('first', [
            'fields'     => [
                'DATE(ts) as date',
                'SUM(total) as amount'
            ],
            'conditions' => $conditions,
            'group'      => ['DATE(Invoice.ts)']
        ]);
        return (!empty($result))?$result[0]:false;
    }

}
