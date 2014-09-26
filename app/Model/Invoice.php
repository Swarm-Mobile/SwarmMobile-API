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
        ]
    ];

    public function biggestTicket ($storeId = null)
    {
        $conditions = [];
        if (isset($storeId) && is_numeric($storeId) && (int) $storeId == $storeId) {
            $conditions['Invoice.store_id'] = $storeId;
        }
        return $this->find('first', [
            'fields'     => [
                'DATE(ts) as date',
                'total as amount'
            ],
            'conditions' => $conditions
        ]);
    }

    public function bestHour ($storeId = null)
    {
        $conditions = [];
        if (isset($storeId) && is_numeric($storeId) && (int) $storeId == $storeId) {
            $conditions['Invoice.store_id'] = $storeId;
        }
        return $this->find('first', [
            'fields'     => [
                "CONCAT(DATE(ts),' ',HOUR(ts),':00:00') as date",
                'SUM(total) as amount'
            ],
            'conditions' => $conditions,
            'group'      => ['DATE(Invoice.ts)', 'HOUR(Invoice.ts)']
        ]);
    }

    public function bestDay ($storeId = null)
    {
        $conditions = [];
        if (isset($storeId) && is_numeric($storeId) && (int) $storeId == $storeId) {
            $conditions['Invoice.store_id'] = $storeId;
        }
        return $this->find('first', [
            'fields'     => [
                'DATE(ts) as date',
                'SUM(total) as amount'
            ],
            'conditions' => $conditions,
            'group'      => ['DATE(Invoice.ts)']
        ]);
    }

}
