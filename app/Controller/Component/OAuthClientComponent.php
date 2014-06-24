<?php

App::uses('Client', 'OAuth.Model');
App::uses('AccessToken', 'OAuth.Model');
App::uses('AuthCode', 'OAuth.Model');
App::uses('RefreshToken', 'OAuth.Model');

class OAuthClientComponent extends Component {

    public $grant_type = 'authorization_code';
    public $access_token = '';
    public $secret = '';
    public $code = '';
    public $client_id = '';
    public $redirect_uri = '';
    public $refresh_token = '';
    public $expires = 0;

    public function __construct() {
        $this->client_id = 'NTMzOGY4ZTYxOGI1YTVi';
        $this->secret = '7cebea7b8d1db5eded8977f1888d404d0a11199b';
        $this->redirect_uri = Router::url('/pages/dump', true);
        $oAuthCodeModel = new AuthCode();
        $oCode = $oAuthCodeModel->find(
                'first', array(
            'conditions' => array('AuthCode.client_id' => $this->client_id),
            'fields' => array('code'),
            'recursive' => -1
                )
        );
        $this->code = $oCode['AuthCode']['code'];
        $oRefreshTokenModel = new RefreshToken();
        $oRefreshToken = $oRefreshTokenModel->find(
                'first', array(
            'conditions' => array('RefreshToken.client_id' => $this->client_id),
            'fields' => array('refresh_token'),
            'recursive' => -1
                )
        );
        $this->refresh_token = $oRefreshToken['RefreshToken']['refresh_token'];
        if (empty($this->refresh_token)) {
            $this->getToken();
        } else {
            $this->refreshToken();
        }
    }

    private function getToken() {
        $postfields = array(
            'client_id' => $this->client_id,
            'client_secret' => $this->secret,
            'code' => $this->code,
            'grant_type' => $this->grant_type,
            'redirect_uri' => $this->redirect_uri
        );
        $result = $this->call('oauth/token', $postfields);
        $this->access_token = $result->access_token;
        $this->expires = (int) $result->expires_in;
        $this->refresh_token = $result->refresh_token;
    }

    private function refreshToken() {
        $postfields = array(
            'refresh_token' => $this->refresh_token,
            'client_id' => $this->client_id,
            'client_secret' => $this->secret,
            'grant_type' => 'refresh_token',
            'redirect_uri' => $this->redirect_uri
        );
        $result = $this->call('oauth/token', $postfields);
        $this->access_token = $result->access_token;
        $this->expires = (int) $result->expires_in;
        $this->refresh_token = ((isset($result->refresh_token)) ? $result->refresh_token : $this->access_token);
    }

    public function call($path, $postfields = array()) {
        if ($path != 'oauth/token') {
            if (empty($this->access_token)) {
                $this->getToken();
            }
            if (time() >= $this->expires) {
                $this->refresh_token();
            }
        }
        $postfields['access_token'] = $this->access_token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, Router::url('/', true) . $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
    }

    public function get($path, $postfields = array()) {
        $postfields['access_token'] = $this->access_token;
        $qry_str = '?';
        foreach ($postfields as $k => $v) {
            $qry_str .= urlencode($k) . '=' . urlencode($v) . '&';
        }
        $qry_str = substr($qry_str, 0, -1);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, Router::url('/', true) . $path . $qry_str);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
    }

    public function multiGet($aPath, $aPostfields, $nocache = 0, $norollups = 0) {
        $node_count = count($aPath);
        $curl_arr = array();
        $master = curl_multi_init();
        for ($i = 0; $i < $node_count; $i++) {
            $aPostfields[$i]['access_token'] = $this->access_token;
            $aPostfields[$i]['norollups'] = $norollups;
            $aPostfields[$i]['nocache'] = $nocache;
            $qry_str = '?';
            foreach ($aPostfields[$i] as $k => $v) {
                $qry_str .= urlencode($k) . '=' . urlencode($v) . '&';
            }
            $qry_str = substr($qry_str, 0, -1);
            $url = 'http://api.swarm-mobile.com/' . $aPath[$i] . $qry_str;
            $curl_arr[$i] = curl_init($url);
            curl_setopt($curl_arr[$i], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_arr[$i], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_arr[$i], CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl_arr[$i], CURLOPT_SSL_VERIFYPEER, 0);
            curl_multi_add_handle($master, $curl_arr[$i]);
        }
        do {
            curl_multi_exec($master, $running);
        } while ($running > 0);
        $aResult = array();
        for ($i = 0; $i < $node_count; $i++) {
            $aResult[] = json_decode(curl_multi_getcontent($curl_arr[$i]));
        }
        curl_multi_close($master);
        return $aResult;
    }

}
