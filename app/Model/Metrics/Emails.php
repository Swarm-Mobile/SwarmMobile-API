<?php


App::uses('MetricModel', 'Model');

class Emails extends MetricModel
{
    
    public $useDbConfig = 'ee';
    public $useTable = 'ws_user_profile';

    public function getFromRaw ()
    {
        if (!$this->validates()) {
            throw new Swarm\ApplicationErrorException(SwarmErrorCodes::MODEL_NOT_INITIALIZED);
        }

        $locationSetting = $this->getLocationSetting();
        $timezone        = $locationSetting->getTimezone();
        $model           = new Model(false, 'ws_user_profile', 'ee');
        $db              = $model->getDataSource();

        $query    = [
            'fields'     => [
                "DISTINCT emailId",
                "CONVERT_TZ(time,'GMT', '$timezone') AS date"
            ],
            'table'      => 'ws_user_profile',
            'alias'      => 'UserProfile',
            'conditions' => [
                'emailId !=' => 'Guest',
                'emailId !=' => '',
                'emailId IS NOT NULL',
                'storeId'    => $this->getLocationId(),
                'time >='    => $this->getStartTime(),
                'time <='    => $this->getEndTime()
            ]
        ];
        $querySQL = $db->buildStatement($query, $model);
        $result   = $db->fetchAll($querySQL);
        $return   = [];
        foreach ($result as $row) {
            $return[] = [
                'email' => $row['UserProfile']['emailId'],
                'time'  => $row[0]['date'],
            ];
        }
        return $return;
    }   

}
