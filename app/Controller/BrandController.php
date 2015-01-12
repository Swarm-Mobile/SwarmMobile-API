<?php

App::uses('Model', 'Model');

class BrandController extends AppController
{

    protected $location;
    protected $invoiceLine;
    protected $locationSetting;

    public function getLocation ()
    {
        if (empty($this->location)) {
            App::uses('Location', 'Model/Location');
            $this->location = new Location();
        }
        return $this->location;
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

    public function setLocation (Location $location)
    {
        $this->location = $location;
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

    public function brands ()
    {
        $errors = AppModel::validationErrors(['location_id'], $this->request->query);
        if (!empty($errors)) {
            throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($errors));
        }
        
        $locationModel = $this->getLocation();
        $locationId    = $this->request->query('location_id');
        $location      = $locationModel->find('first', ['conditions' => ['Location.id' => $locationId]]);
        if (empty($location)) {
            throw new Swarm\UnprocessableEntityException(SwarmErrorCodes::BRAND_BRANDS_LOCATION_NOTFOUND);
        }
        $locationSetting = $this->getLocationSetting();
        $locationSetting->setLocationId($locationId);
        $storeId         = $locationSetting->getSettingValue(LocationSetting::POS_STORE_ID);
        if (empty($storeId)) {
            throw new Swarm\UnprocessableEntityException(SwarmErrorCodes::BRAND_BRANDS_STORE_NOTFOUND);
        }
        $invoiceLine = $this->getInvoiceLine();
        $brands      = $invoiceLine->find('all', [
            'fields'     => ['DISTINCT InvoiceLine.family'],
            'conditions' => [
                'InvoiceLine.store_id'  => $storeId,
                'InvoiceLine.family !=' => ['']
            ]
        ]);
        $result      = [];
        if (!empty($brands)) {
            foreach ($brands as $brand) {
                $result[] = ucwords(strtolower($brand['InvoiceLine']['family']));
            }
        }
        return new JsonResponse(['body' => $result]);
    }

}
