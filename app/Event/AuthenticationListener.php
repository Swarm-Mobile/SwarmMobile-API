<?php

App::uses('RedisComponent', 'Controller/Component');
App::uses('OAuthClientComponent', 'Controller/Component');
App::uses('OAuthComponent', 'OAuth.Controller/Component');
App::uses('CakeEventListener', 'Event');
App::uses('CakeSession', 'Model/Datasource');
App::uses('ClassRegistry', 'Utility');
App::uses('CakeEventManager', 'Event');
App::uses('CakeEvent', 'Event');

class AuthenticationListener implements CakeEventListener
{

    /**
     * @return array associative array or event key names pointing to the function
     * that should be called in the object when the respective event is fired
     */
    public function implementedEvents ()
    {
        return [
            'Dispatcher.beforeDispatch' => [
                'callable' => 'fireAuthEvents',
                'priority' => '1'
            ],
        ];
    }

    public function fireAuthEvents (CakeEvent $event)
    {
        $this->authenticateRequest($event);        
    }

    private function cURLCall ($params)
    {
        $query = '';
        foreach ($params as $k => $v) {
            $query .= $k . '=' . $v . '&';
        }
        $query = substr($query, 0, -1);

        $request  = curl_init();
        curl_setopt($request, CURLOPT_URL, $this->authURL);
        curl_setopt($request, CURLOPT_USERPWD, $this->authUsername . ':' . $this->authPassword);
        curl_setopt($request, CURLOPT_POST, count($params));
        curl_setopt($request, CURLOPT_POSTFIELDS, $query);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($request);
        curl_close($request);
        return $response;
    }

    private function authenticateRequest (CakeEvent $event)
    {
        $params      = $event->data['request']->query;
        $accessToken = $params['access_token'];

        $predis       = RedisComponent::getInstance('oAuth');
        $oauthStorage = new OAuth2\Storage\Redis($predis);
        $token        = $oauthStorage->getAccessToken($accessToken);

//        $token = [
//            'expires' => 99999999999,
//            'user_id' => 25
//        ];

        try {
            if (empty($token)) {
                $oOAuth = new OAuthComponent(new ComponentCollection());
                $oOAuth->OAuth2->verifyAccessToken($accessToken);
            }
            else {
                if ($token['expires'] <= time()) {
                    throw new Exception('The access token provided is invalid.');
                }
                else {                   
                    $event = new CakeEvent(
                        'Authentication.passed', 
                        $this, 
                        array_merge($token, $params)
                    );
                    CakeEventManager::instance()->dispatch($event);
                }
            }
        }
        catch (Exception $e) {
            header("HTTP/1.1 403");
            header("Content-Type: application/json");
            header("Cache-Control: no-store");
            echo json_encode([
                'error'             => 'invalid_token',
                'error_description' => $e->getMessage()
                    ], true);
            exit();
        }
    }

}
