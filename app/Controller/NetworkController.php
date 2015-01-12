<?php

App::uses('Model', 'Model');
App::uses('MetricModel', 'Model/Metrics');
App::uses('LocationSetting', 'Model/Location');
App::uses('MetricFormatComponent', 'Controller/Component');

class NetworkController extends AppController
{

    protected $wifiConnections;
    protected $emailsCaptured;
    protected $emails;
    protected $websites;

    public function getWifiConnections ()
    {
        if (empty($this->wifiConnections)) {
            App::uses('WifiConnections', 'Model/Metrics');
            $this->wifiConnections = new WifiConnections();
        }
        return $this->wifiConnections;
    }

    public function getEmailsCaptured ()
    {
        if (empty($this->emailsCaptured)) {
            App::uses('EmailsCaptured', 'Model/Metrics');
            $this->emailsCaptured = new EmailsCaptured();
        }
        return $this->emailsCaptured;
    }

    public function getEmails ()
    {
        if (empty($this->emails)) {
            App::uses('Emails', 'Model/Metrics');
            $this->emails = new Emails();
        }
        return $this->emails;
    }

    public function getWebsites ()
    {
        if (empty($this->websites)) {
            App::uses('Websites', 'Model/Metrics');
            $this->websites = new Websites();
        }
        return $this->websites;
    }

    public function setWifiConnections (WifiConnections $wifiConnections)
    {
        $this->wifiConnections = $wifiConnections;
        return $this;
    }

    public function setEmailsCaptured (EmailsCaptured $emailsCaptured)
    {
        $this->emailsCaptured = $emailsCaptured;
        return $this;
    }

    public function setEmails (Emails $emails)
    {
        $this->emails = $emails;
        return $this;
    }

    public function setWebsites (Websites $websites)
    {
        $this->websites = $websites;
        return $this;
    }

    private function _nightclubHoursSwitch ($data, $locationSetting)
    {
        $nightclubHours = $locationSetting->getSettingValue(LocationSetting::NIGHTCLUB_HOURS);
        if ($nightclubHours === 'yes') {
            $timezone          = $locationSetting->getTimezone();
            $nightclubLocation = $locationSetting->getSettingValue(LocationSetting::NIGHTCLUB_HOURS_LOCATION);
            $data              = MetricFormatComponent::nightclubHoursSwitch($data, $timezone, $nightclubHours, $nightclubLocation);
        }
        return $data;
    }

    private function _formatResult (MetricModel $model, $endpoint, $data)
    {
        return [
            'data'    => $data,
            'options' => [
                'endpoint'    => '/location/' . $endpoint,
                'location_id' => $model->getLocationId(),
                'start_date'  => $model->getStartDate(),
                'end_date'    => $model->getEndDate()
            ]
        ];
    }

    public function wifiConnections ()
    {
        $wifiConnections = $this->getWifiConnections();
        $wifiConnections->create($this->request->query, true);
        if ($wifiConnections->validates()) {
            $locationSetting          = $wifiConnections->getLocationSetting();
            $wifiConnectionsResultset = $wifiConnections->getFromCache();
            $data = MetricFormatComponent::formatAsSum(
                $wifiConnections->getStartDate(), 
                $wifiConnections->getEndDate(), 
                $wifiConnectionsResultset, $locationSetting->getOpenHours()
            );
            $data = $this->_nightclubHoursSwitch($data, $locationSetting);
            return new JsonResponse(['body' => $this->_formatResult($wifiConnections, __FUNCTION__, $data)]);
        }
        throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($wifiConnections->validationErrors));
    }

    public function emailsCaptured ()
    {
        $emailsCaptured = $this->getEmailsCaptured();
        $emailsCaptured->create($this->request->query, true);
        if ($emailsCaptured->validates()) {
            $locationSetting         = $emailsCaptured->getLocationSetting();
            $emailsCapturedResultset = $emailsCaptured->getFromCache();
            $data                    = MetricFormatComponent::formatAsSum(
                $emailsCaptured->getStartDate(), 
                $emailsCaptured->getEndDate(), 
                $emailsCapturedResultset, 
                $locationSetting->getOpenHours()
            );
            $data = $this->_nightclubHoursSwitch($data, $locationSetting);
            return new JsonResponse(['body' => $this->_formatResult($emailsCaptured, __FUNCTION__, $data)]);
        }
        throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($emailsCaptured->validationErrors));
    }

    public function emails ()
    {
        $emails = $this->getEmails();
        $emails->create($this->request->query, true);
        if ($emails->validates()) {
            $result = [
                'data'    => $emails->getFromRaw(),
                'options' => [
                    'endpoint'    => '/location/emails',
                    'location_id' => $emails->getLocationId(),
                    'start_date'  => $emails->getStartDate(),
                    'end_date'    => $emails->getEndDate()
                ]
            ];
            return new JsonResponse(['body' => $result]);
        }
        throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($emails->validationErrors));
    }

    public function websites ()
    {
        $websites = $this->getWebsites();
        $websites->create($this->request->query, true);
        if ($websites->validates()) {
            $result = [
                'data'    => $websites->getFromRaw(),
                'options' => [
                    'endpoint'    => '/location/websites',
                    'location_id' => $websites->getLocationId(),
                    'start_date'  => $websites->getStartDate(),
                    'end_date'    => $websites->getEndDate()
                ]
            ];
            return new JsonResponse(['body' => $result]);
        }
        throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($websites->validationErrors));
    }

}
