<?php

App::uses('RedisComponent', 'Controller/Component');
App::uses('CakeEventListener', 'Event');
App::uses('Location', 'Model');
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

    public function validation (CakeEvent $event)
    {
        try {
            if (isset($event->data['location_id'])) {
                $this->validateLocationId($event->data['token'], $event->data['location_id']);
            }
            if (isset($event->data['customer_id'])) {
                $this->validateCustomerId($event->data['token'], $event->data['customer_id']);
            }
        }
        catch (OAuth2AuthenticateException $e) {
            $this->response_code    = $e->getCode();
            $this->response_message = $e->getMessage();
            $e->sendHttpResponse();
            return false;
        }
    }

    private function validateLocationId ($token, $location_id)
    {
        $user    = new User();
        $user->read(null, $token['user_id']);
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

    private function validateCustomerId ($token, $customer_id)
    {
        $user = new User();
        $user->read(null, $token['user_id']);
        switch ($user->data['User']['usertype_id']) {
            case UserType::$SUPER_ADMIN:
            case UserType::$ACCOUNT_MANAGER:
                return true;
            case UserType::$RESELLER:
            case UserType::$LOCATION_MANAGER:
            case UserType::$DEVELOPER:
            case UserType::$EMPLOYEE:
                $tokenLocations    = $user->getLocationList();
                $this->Customer->readFromParams(['customer_id' => $customer_id]);
                $settings          = $this->LocationSetting->find('all', [
                    'conditions' => [
                        'value'      => $this->Customer->data['Customer']['store_id'],
                        'setting_id' => settId('pos_store_id')
                    ]]
                );
                $customerLocations = [];
                foreach ($settings as $setting) {
                    $customerLocations = $setting['LocationSetting']['location_id'];
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
