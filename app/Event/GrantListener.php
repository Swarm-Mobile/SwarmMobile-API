<?php

require_once __DIR__ . '/../Controller/Component/CompressedFunctions.php';

App::uses('RedisComponent', 'Controller/Component');
App::uses('CakeEventListener', 'Event');
App::uses('Location', 'Model');
App::uses('LocationSetting', 'Model');
App::uses('Customer', 'Model');
App::uses('User', 'Model');
App::uses('UserType', 'Model');

class GrantListener implements CakeEventListener
{

    /**
     * @return array associative array or event key names pointing to the function
     * that should be called in the object when the respective event is fired
     */
    public function implementedEvents ()
    {
        return [
            'Authentication.passed' => [
                'callable' => 'validation',
                'priority' => '1'
            ],
        ];
    }

    /**
     * Checks if the Access Token user can 
     * access to the resource that he/she is
     * requesting 
     * 
     * @param CakeEvent $event     
     */
    public function validation (CakeEvent $event)
    {        
        try {
            if (isset($event->data['location_id'])) {
                $this->validateLocationId($event->data['user_id'], $event->data['location_id']);
            }
            if (isset($event->data['customer_id'])) {
                $this->validateCustomerId($event->data['user_id'], $event->data['customer_id']);
            }
        }
        catch (OAuth2AuthenticateException $e) {
            $this->response_code    = $e->getCode();
            $this->response_message = $e->getMessage();
            $e->sendHttpResponse();
            return false;
        }
    }

    /**
     * Validates if a certain user_id have access
     * to a certain location_id.
     * 
     * @param int $user_id
     * @param int $location_id
     * @throws Exception
     */
    private function validateLocationId ($user_id, $location_id)
    {        
        $user    = new User();
        $user->read(null, $user_id);
        $isValid = false;
        switch ($user->data['User']['usertype_id']) {
            case UserType::$SUPER_ADMIN:
            case UserType::$ACCOUNT_MANAGER:
                $isValid = true;
                break;
            case UserType::$RESELLER:
            case UserType::$LOCATION_MANAGER:
            case UserType::$DEVELOPER:
            case UserType::$EMPLOYEE:
            case UserType::$GUEST:
            default:
                $isValid = in_array($location_id, $user->getLocationList());
                break;
        }
        if (!$isValid) {
            throw new Exception('You are not allowed to access to this location_id.');
        }
    }

    /**
     * Valiates if a certain user_id have access
     * to a certain customer_id
     *      
     * @param type $user_id
     * @param type $customer_id
     * @return boolean
     * @throws Exception
     */
    private function validateCustomerId ($user_id, $customer_id)
    {
        $user = new User();
        $user->read(null, $user_id);
        switch ($user->data['User']['usertype_id']) {
            case UserType::$SUPER_ADMIN:
            case UserType::$ACCOUNT_MANAGER:
                return true;
            case UserType::$RESELLER:
            case UserType::$LOCATION_MANAGER:
            case UserType::$DEVELOPER:
            case UserType::$EMPLOYEE:
                $tokenLocations    = $user->getLocationList();
                $customer = new Customer();
                $customer->readFromParams(['customers_id' => $customer_id]);
                $locationSetting = new LocationSetting();
                $settings          = $locationSetting->find('all', [
                    'conditions' => [
                        'value'      => $customer->data['Customer']['store_id'],
                        'setting_id' => settId('pos_store_id')
                    ]]
                );
                $customerLocations = [];
                foreach ($settings as $setting) {
                    $customerLocations[] = $setting['LocationSetting']['location_id'];
                }                
                $intersect = array_intersect($customerLocations, $tokenLocations);                
                if (count($intersect) > 0) {
                    return true;
                }
            case UserType::$GUEST:
            default:
                throw new Exception('You are not allowed to access to this location_id.');
        }
    }

}
