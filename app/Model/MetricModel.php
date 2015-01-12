<?php

App::uses('Model', 'Model');
App::uses('ValidatorComponent', 'Controller/Component');
App::uses('LocationSetting', 'Model/Location');
App::uses('MetricFormatComponent', 'Controller/Component');
App::uses('TimeComponent', 'Controller/Component');

abstract class MetricModel extends AppModel
{

    public $primaryKey  = 'id';
    public $id          = 'id';
    public $useDbConfig = 'rollups';
    public $useTable    = false;
    protected $locationId;
    protected $timezone;
    protected $startDate;
    protected $endDate;
    protected $startTime;
    protected $endTime;
    protected $locationSetting;

    public function __construct ($id = false, $table = null, $ds = null)
    {
        $this->setValidationFields(['location_id', 'start_date', 'end_date']);
        parent::__construct($id, $table, $ds);
    }

    public function getLocationId ()
    {
        return $this->locationId;
    }

    public function setLocationId ($locationId)
    {
        if (!ValidatorComponent::isPositiveInt($locationId)) {
            throw new InvalidArgumentException(SwarmErrorCodes::setError('locationId must be a positive integer'));
        }
        $this->locationId = $locationId;
        return $this;
    }

    public function getTimezone ()
    {
        return $this->timezone;
    }

    public function setTimezone ($timezone)
    {
        if (!ValidatorComponent::isTimezone($timezone)) {
            $timezone = 'America/Los_Angeles';
        }
        $this->timezone = $timezone;
        return $this;
    }

    public function getStartTime ()
    {
        return $this->startTime;
    }

    public function setStartTime ($startTime)
    {
        if (!ValidatorComponent::isDate($startTime, 'Y-m-d H:i:s')) {
            throw new InvalidArgumentException(
            SwarmErrorCodes::setError('startTime must be a valid yyyy-mm-dd hh:ii:ss string')
            );
        }
        $this->startTime = $startTime;
        return $this;
    }

    public function getStartDate ()
    {
        return $this->startDate;
    }

    public function setStartDate ($startDate)
    {
        if (!ValidatorComponent::isDate($startDate)) {
            throw new InvalidArgumentException(
            SwarmErrorCodes::setError('startDate must be a valid yyyy-mm-dd string')
            );
        }
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate ()
    {
        return $this->endDate;
    }

    public function setEndDate ($endDate)
    {
        if (!ValidatorComponent::isDate($endDate)) {
            throw new InvalidArgumentException(
            SwarmErrorCodes::setError('endDate must be a valid yyyy-mm-dd string')
            );
        }
        $this->endDate = $endDate;
        return $this;
    }

    public function getEndTime ()
    {
        return $this->endTime;
    }

    public function setEndTime ($endTime)
    {
        if (!ValidatorComponent::isDate($endTime, 'Y-m-d H:i:s')) {
            throw new InvalidArgumentException(
            SwarmErrorCodes::setError('endTime must be a valid yyyy-mm-dd hh:ii:ss string')
            );
        }
        $this->endTime = $endTime;
        return $this;
    }

    public function getLocationSetting ()
    {
        return $this->locationSetting;
    }

    public function setLocationSetting (\LocationSetting $locationSetting)
    {
        $this->locationSetting = $locationSetting;
        return $this;
    }

    public function needsSessionTable ($sessionTable)
    {
        $method      = ($sessionTable == 'sessions_archive') ? 'getStartTime' : 'getEndTime';
        list($date) = explode(' ', $this->{$method}());
        $datetime    = new DateTime($date);
        $currentDate = new DateTime();
        $diff        = date_diff($datetime, $currentDate);
        return ($sessionTable == 'sessions_archive') ? $diff->days >= 150 : $diff->days <= 150;
    }

    public function create ($data = [], $filterKey = false)
    {
        parent::create($data, $filterKey);
        if ($this->validates()) {
            $this->setLocationId($this->data[get_class($this)]['location_id']);
            $this->setStartDate($this->data[get_class($this)]['start_date']);
            $this->setEndDate($this->data[get_class($this)]['end_date']);
            $this->setLocationSetting(new LocationSetting());
            $this->locationSetting->create(['LocationSetting' => ['location_id' => $this->locationId]], true);
            $this->setTimezone($this->locationSetting->getSettingValue(LocationSetting::TIMEZONE));
            $this->setStartTime(TimeComponent::convertTimeToGMT($this->startDate . ' 00:00:00', $this->timezone));
            $this->setEndTime(TimeComponent::convertTimeToGMT($this->endDate . ' 23:59:59', $this->timezone));
        }
    }

    abstract function getFromRaw ();

    function incby ($fields, $incby, $locationId, $date)
    {
        $set = '';
        if ($this->id > 0) {
            foreach ($fields as $field) {
                $set.= "$field = $field + $incby,";
            }
            $set  = substr($set, 0, -1);
            $sSQL = <<<SQL
UPDATE {$this->useTable} 
    SET $set 
    WHERE date = :date 
    AND location_id = :location_id
SQL;
        }
        else {
            foreach ($fields as $field) {
                $set.= "$field = $incby , ";
            }
            $sSQL = <<<SQL
INSERT INTO {$this->useTable}
    SET $set 
        date = :date ,
        location_id = :location_id
SQL;
        }
        $db     = $this->getDataSource();
        $db->execute($sSQL, [], ['date' => $date, 'location_id' => $locationId]);
    }

}
