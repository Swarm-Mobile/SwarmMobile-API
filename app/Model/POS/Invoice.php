<?php

class Invoice extends AppModel
{

    public $useDbConfig = 'pos';
    public $useTable    = 'invoices';
    public $primaryKey  = 'invoice_id';
    public $id          = 'invoice_id';

    public function biggestTicket ($storeId = null, $startDate = null, $endDate = null, $locationTimezone = 'America/Los_Angeles')
    {
        $conditions = [];
        if (!ValidatorComponent::isTimezone($locationTimezone)) {
            throw new InvalidArgumentException('Incorrect Location Timezone');
        }
        else {
            $tz = new DateTimeZone($locationTimezone);
        }
        if (ValidatorComponent::isPositiveInt($storeId)) {
            $conditions['Invoice.store_id'] = $storeId;
        }
        $gmt = new DateTimeZone('GMT');
        if (ValidatorComponent::isDate($startDate)) {
            $startDateTime               = new DateTime($startDate . ' 00:00:00', $tz);
            $conditions['Invoice.ts >='] = $startDateTime->setTimezone($gmt)->format('Y-m-d H:i:s');
        }
        if (ValidatorComponent::isDate($endDate)) {
            $endDateTime                 = new DateTime($endDate . ' 23:59:59', $tz);
            $conditions['Invoice.ts <='] = $endDateTime->setTimezone($gmt)->format('Y-m-d H:i:s');
        }
        $this->recursive = -1;
        $result          = $this->find('first', [
            'fields'     => [
                'DATE(CONVERT_TZ(ts, "GMT", "' . $locationTimezone . '")) as date',
                'total as amount'
            ],
            'order'      => 'amount DESC',
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

    public function bestHour ($storeId = null, $startDate = null, $endDate = null, $locationTimezone = 'America/Los_Angeles')
    {
        $conditions = [];
        if (!ValidatorComponent::isTimezone($locationTimezone)) {
            throw new InvalidArgumentException('Incorrect Location Timezone');
        }
        else {
            $tz = new DateTimeZone($locationTimezone);
        }
        if (ValidatorComponent::isPositiveInt($storeId)) {
            $conditions['Invoice.store_id'] = $storeId;
        }
        $gmt = new DateTimeZone('GMT');
        if (ValidatorComponent::isDate($startDate)) {
            $startDateTime               = new DateTime($startDate . ' 00:00:00', $tz);
            $conditions['Invoice.ts >='] = $startDateTime->setTimezone($gmt)->format('Y-m-d H:i:s');
        }
        if (ValidatorComponent::isDate($endDate)) {
            $endDateTime                 = new DateTime($endDate . ' 23:59:59', $tz);
            $conditions['Invoice.ts <='] = $endDateTime->setTimezone($gmt)->format('Y-m-d H:i:s');
        }
        $result = $this->find('first', [
            'fields'     => [
                "CONCAT(DATE(CONVERT_TZ(ts, 'GMT', '$locationTimezone')),' ',HOUR(CONVERT_TZ(ts, 'GMT', '$locationTimezone')),':00:00') as date",
                'SUM(total) as amount'
            ],
            'order'      => 'amount DESC',
            'conditions' => $conditions,
            'group'      => [
                'DATE(CONVERT_TZ(ts, "GMT", "' . $locationTimezone . '"))',
                'HOUR(CONVERT_TZ(ts, "GMT", "' . $locationTimezone . '"))'
            ]
        ]);
        return (!empty($result)) ? $result[0] : false;
    }

    public function bestDay ($storeId = null, $startDate = null, $endDate = null, $locationTimezone = 'America/Los_Angeles')
    {
        $conditions = [];
        if (!ValidatorComponent::isTimezone($locationTimezone)) {
            throw new InvalidArgumentException('Incorrect Location Timezone');
        }
        else {
            $tz = new DateTimeZone($locationTimezone);
        }
        if (ValidatorComponent::isPositiveInt($storeId)) {
            $conditions['Invoice.store_id'] = $storeId;
        }
        $gmt = new DateTimeZone('GMT');
        if (ValidatorComponent::isDate($startDate)) {
            $startDateTime               = new DateTime($startDate . ' 00:00:00', $tz);
            $conditions['Invoice.ts >='] = $startDateTime->setTimezone($gmt)->format('Y-m-d H:i:s');
        }
        if (ValidatorComponent::isDate($endDate)) {
            $endDateTime                 = new DateTime($endDate . ' 23:59:59', $tz);
            $conditions['Invoice.ts <='] = $endDateTime->setTimezone($gmt)->format('Y-m-d H:i:s');
        }
        $result = $this->find('first', [
            'fields'     => [
                'DATE(CONVERT_TZ(ts, "GMT", "' . $locationTimezone . '")) as date',
                'SUM(total) as amount'
            ],
            'order'      => 'amount DESC',
            'conditions' => $conditions,
            'group'      => [
                'DATE(CONVERT_TZ(ts, "GMT", "' . $locationTimezone . '"))'
            ]
        ]);
        return (!empty($result)) ? $result[0] : false;
    }

}
