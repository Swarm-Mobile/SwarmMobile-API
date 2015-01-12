<?php

App::uses('Returning', 'Model/Metrics');
App::uses('PresenceReturningByDate', 'Model/Metrics');
App::uses('PresenceReturningByHour', 'Model/Metrics');

class PresenceReturning extends Returning
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
        $byDateModel  = new PresenceReturningByDate();
        $byDateModel->create([
            'location_id' => $this->getLocationId(),
            'start_date'  => $this->getStartDate(),
            'end_date'    => $this->getEndDate(),
                ], true);
        $byDateResult = $byDateModel->getFromCache();

        $byHourModel  = new PresenceReturningByHour();
        $byHourModel->create([
            'location_id' => $this->getLocationId(),
            'start_date'  => $this->getStartDate(),
            'end_date'    => $this->getEndDate(),
                ], true);
        $byHourResult = $byHourModel->getFromCache();
        $return       = $byHourResult;
        foreach ($byHourResult as $date => $row) {
            if (isset($byDateResult[$date])) {
                $return[$date]['total_total'] = $byDateResult[$date]['total'];
            }
        }
        return $return;
    }

}
