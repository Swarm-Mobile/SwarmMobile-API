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
    
    /**
     * Hack to call to more than one callback function
     * when the event is triggered.
     * 
     * @param CakeEvent $event
     */
    public function fireAuthEvents (CakeEvent $event)
    {
        $this->authenticateRequest($event);
    }

    /**
     * Checks if the OAuth token received in the request
     * is a valid OAuth token.
     * 
     * @param CakeEvent $event
     * @throws Exception
     */
    private function authenticateRequest (CakeEvent $event)
    {
        $params      = $event->data['request']->query;
        $accessToken = $params['access_token'];

        $predis       = RedisComponent::getInstance('oAuth');
        $oauthStorage = new OAuth2\Storage\Redis($predis);
        $token        = $oauthStorage->getAccessToken($accessToken);

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
                        'Authentication.passed', $this, array_merge($token, $params)
                    );
                    CakeEventManager::instance()->dispatch($event);
                }
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
            echo json_encode([
                'error'             => 'invalid_token',
                'error_description' => $e->getMessage()
                    ], true);
            exit();
        }
    }

}
