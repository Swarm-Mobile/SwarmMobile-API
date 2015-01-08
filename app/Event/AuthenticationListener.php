<?php

App::uses('RedisComponent', 'Controller/Component');
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
                'priority' => '2'
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
        $authType = $this->getAuthenticationType($event);
        switch ($authType) {
            case 'oauth':
                return $this->authenticateOAuthRequest($event);                
            case 'hmac':
                return $this->authenticateHMACRequest($event);                
            case 'no-auth':
                return;
            default:
                $this->renderException(new SwarmNotFoundException('Endpoint not found', 404));
                break;
        }
    }

    private function renderException (Exception $e)
    {
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

    private function getAuthenticationType ($event)
    {
        $hmacEndpoints   = ['\/what_is_here'];
        $noAuthEndpoints = ['\/oauth\/', '\/logout', '\/login', '\/user\/register', '\/server_health\/ok'];

        foreach ($hmacEndpoints as $endpoint) {
            if (preg_match('/' . $endpoint . '/', $_SERVER['REQUEST_URI'])) {
                return 'hmac';
            }
        }

        foreach ($noAuthEndpoints as $endpoint) {
            if (preg_match('/' . $endpoint . '/', $_SERVER['REQUEST_URI'])) {
                return 'no-auth';
            }
        }

        return 'oauth';
    }

    /**
     * Checks if the OAuth token received in the request
     * is a valid OAuth token.
     * 
     * @param CakeEvent $event
     * @throws Exception
     */
    private function authenticateOAuthRequest (CakeEvent $event)
    {
        try {
            $params = $event->data['request']->query;
            if (!isset($params['access_token'])) {
                $params = $event->data['request']->data;
                if (!isset($params['access_token'])) {
                    throw new Swarm\UnauthorizedException(
                        SwarmErrorCodes::setError('The access token provided is invalid.')
                    );
                }
            }
            $accessToken  = $params['access_token'];
            $predis       = RedisComponent::getInstance('oAuth');
            $oauthStorage = new OAuth2\Storage\Redis($predis);
            $token        = $oauthStorage->getAccessToken($accessToken);
            if (empty($token)) {
                $oOAuth = new OAuthComponent(new ComponentCollection());
                $oOAuth->OAuth2->verifyAccessToken($accessToken);
            }
            else {
                if ($token['expires'] <= time()) {
                    throw new Swarm\UnauthorizedException(
                        SwarmErrorCodes::setError('The access token provided is invalid.')
                    );
                }
                else {
                    $event = new CakeEvent('Authentication.passed', $this, array_merge($token, $params));
                    CakeEventManager::instance()->dispatch($event);
                }
            }
        }
        catch (Exception $e) {
            $this->renderException($e);
        }
    }

    private function authenticateHMACRequest ($event)
    {
        App::uses('Partner', 'Model/User');

        $request   = $event->data['request'];
        $date      = $request->header('Swarm-Timestamp');
        $remoteId  = $request->header('Swarm-Remote-Id');
        $partnerId = $request->header('Swarm-Partner-Id');
        $signature = $request->header('Swarm-Api-Challange');

        if (empty($remoteId)) {
            $remoteId = "none";
        }

        $partnerModel = new Partner();
        $partner      = $partnerModel->find('first', [
            'conditions' => [
                'name' => $partnerId,
            ]
        ]);       

        if (empty($partner)) {
            $this->renderException(new UnauthorizedException('Invalid Partner', 401));
        }
        else {
            $key       = $partner['Partner']['key'];
            $stepOne   = base64_encode(hash_hmac('sha256', $partnerId, $key, true));
            $stepTwo   = base64_encode(hash_hmac('sha256', $stepOne, $remoteId, true));
            $stepThree = base64_encode(hash_hmac('sha256', $stepTwo, $date, true));                        
            if ($signature !== $stepThree) {
                $this->renderException(new UnauthorizedException('Invalid HMAC signature', 401));
            }
        }
    }

}
