<?php

App::uses('CakeEventListener', 'Event');
App::uses('Location', 'Model/Location');
App::uses('LocationSetting', 'Model/Location');
App::uses('Customer', 'Model/POS');
App::uses('User', 'Model/User');
App::uses('UserType', 'Model/User');

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
            if (isset($event->data['uuid'])) {
                $this->validateUUID($event->data['user_id'], $event->data['uuid']);
            }
        }
        catch (Exception $e) {
            header("HTTP/1.1 403");
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Methods: POST, GET");
            header("Access-Control-Allow-Headers: X-PINGOTHER");
            header("Content-Type: application/json; charset=UTF-8");
            header("Access-Control-Max-Age: 1728000");
            header("Pragma: no-cache");
            header("Cache-Control: no-store; no-cache;must-revalidate; post-check=0; pre-check=0");
            echo json_encode(
                    [
                'error'             => 'invalid_param',
                'error_description' => $e->getMessage()
                    ], true
            );
            exit();
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
        if (isset($user->data['User'])) {
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
        }
        if (!$isValid) {
            throw new Swarm\UnauthorizedException(
            SwarmErrorCodes::setError('You are not allowed to access to this location_id.')
            );
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
                $tokenLocations = $user->getLocationList();
                $customer       = new Customer();
                $customer->read(null, $customer_id);
                if (empty($customer->data)) {
                    throw new Swarm\UnauthorizedException(
                    SwarmErrorCodes::setError('Incorrect customer_id')
                    );
                }
                $locationSetting   = new LocationSetting();
                $settings          = $locationSetting->find('all', [
                    'conditions' => [
                        'value'      => $customer->data['Customer']['store_id'],
                        'setting_id' => LocationSetting::POS_STORE_ID
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
                throw new Swarm\UnauthorizedException(
                SwarmErrorCodes::setError('You are not allowed to access to this location_id.')
                );
        }
    }

    /**
     * Valiates if a certain user_id a certain uuid
     *      
     * @param type $user_id
     * @param type uuid
     * @return boolean
     * @throws Exception
     */
    private function validateUUID ($user_id, $uuid)
    {
        $user = new User();
        $user->read(null, $user_id);
        if ($user->data['User']['uuid'] != $uuid) {
            throw new Swarm\UnauthorizedException(
            SwarmErrorCodes::setError("UUID don't match with the token user_id.")
            );
        }
        return true;
    }

}
