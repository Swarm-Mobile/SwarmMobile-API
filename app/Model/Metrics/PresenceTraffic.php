<?php

App::uses('FootTraffic', 'Model/Metrics');
App::uses('PresenceTrafficByDate', 'Model/Metrics');
App::uses('PresenceTrafficByHour', 'Model/Metrics');

class PresenceTraffic extends FootTraffic
{

    public $useTable = false;

    public function getFromRaw ()
    {
        throw new Swarm\ApplicationErrorException(SwarmErrorCodes::METHOD_NOT_IMPLEMENTED);
    }

    public function storeInCache ($result = [])
    {
        throw new Swarm\ApplicationErrorException(SwarmErrorCodes::METHOD_NOT_IMPLEMENTED);
    }

    public function getFromCache ()
    {
        if (!$this->validates()) {            
            throw new Swarm\ApplicationErrorException(SwarmErrorCodes::MODEL_NOT_INITIALIZED);
        }        
        $byDateModel  = new PresenceTrafficByDate();
        $byDateModel->create([
            'location_id' => $this->getLocationId(),
            'start_date'  => $this->getStartDate(),
            'end_date'    => $this->getEndDate(),
                ], true);
        $byDateResult = $byDateModel->getFromCache();

        $byHourModel  = new PresenceTrafficByHour();
        $byHourModel->create([
            'location_id' => $this->getLocationId(),
            'start_date'  => $this->getStartDate(),
            'end_date'    => $this->getEndDate(),
                ], true);
        $byHourResult = $byHourModel->getFromCache();
        $return       = $byHourResult;
        foreach ($byHourResult as $date => $row) {
            if(isset($byDateResult[$date])){
                $return[$date]['total_total'] = $byDateResult[$date]['total'];                
            }
        }
        return $return;
    }

}
