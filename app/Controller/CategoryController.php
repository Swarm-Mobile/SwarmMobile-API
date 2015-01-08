<?php

App::uses('Model', 'Model');
App::uses('Location', 'Model/Location');
App::uses('Category', 'Model/POS');

class CategoryController extends AppController
{

    protected $category;
    protected $location;
    protected $locationSetting;

    public function getCategory ()
    {
        if (empty($this->category)) {
            App::uses('Category', 'Model/POS');
            $this->category = new Category();
        }
        return $this->category;
    }

    public function getLocation ()
    {
        if (empty($this->location)) {
            App::uses('Location', 'Model/Location');
            $this->location = new Location();
        }
        return $this->location;
    }

    public function getLocationSetting ()
    {
        if (empty($this->locationSetting)) {
            App::uses('LocationSetting', 'Model/Location');
            $this->locationSetting = new LocationSetting();
        }
        return $this->locationSetting;
    }

    public function setCategory (Category $category)
    {
        $this->category = $category;
        return $this;
    }

    public function setLocation (Location $location)
    {
        $this->location = $location;
        return $this;
    }

    public function setLocationSetting (LocationSetting $locationSetting)
    {
        $this->locationSetting = $locationSetting;
        return $this;
    }

    public function categories ()
    {
        $errors = AppModel::validationErrors(['location_id'], $this->request->query);
        if (!empty($errors)) {
            throw new Swarm\RequestValidationException(SwarmErrorCodes::getFirstError($errors));
        }
        $locationModel = $this->getLocation();
        $locationId    = $this->request->query('location_id');
        $location      = $locationModel->find('first', ['conditions' => ['Location.id' => $locationId]]);
        if (empty($location)) {
            throw new Swarm\UnprocessableEntityException(SwarmErrorCodes::CATEGORY_CATEGORIES_LOCATION_NOTFOUND);
        }
        $locationSetting = $this->getLocationSetting();
        $locationSetting->create(['location_id' => $locationId], true);
        $storeId         = $locationSetting->getSettingValue(LocationSetting::POS_STORE_ID);
        if (empty($storeId)) {
            throw new Swarm\UnprocessableEntityException(SwarmErrorCodes::CATEGORY_CATEGORIES_STORE_NOTFOUND);
        }
        $category   = $this->getCategory();
        $categories = $category->find('all', [
            'fields'     => ['DISTINCT Category.name'],
            'conditions' => [
                'Category.store_id' => $storeId,
                'Category.name !='  => ['']
            ]
        ]);
        $result     = [];
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $result[] = ucwords(strtolower($category['Category']['name']));
            }
        }
        return new JsonResponse(['body' => $result]);
    }

}
