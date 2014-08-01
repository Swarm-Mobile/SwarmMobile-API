<?php

class LogComponent {

//    private function call_log() {
//        $this->request_end = date('Y-m-d H:i:s');
//        $oModel = new Model(false, 'calls', 'rollups');
//        $call = array(
//            'id_user' => $this->user['id_user'],
//            'username' => $this->user['username'],
//            'endpoint' => $this->endpoint,
//            'request_start' => $this->request_start,
//            'request_end' => $this->request_end,
//            'response_time' => microtime(true) - $this->microtime,
//            'response_code' => $this->response_code,
//            'response_message' => $this->response_message,
//            'params' => $this->params
//        );
//        $oModel->save($call);
//    }

    private function call_log($component, $function, $request_method) {
        $file = __DIR__ . '/../tmp/logs/api_calls/' . date('Y_m_d_H_i_s') .
                '_' . strtoupper($request_method) . '_' . $component . '_' . $function;
        $post = var_export($_POST, true);
        $get = var_export($_GET, true);
        $text = <<<TEXT
POST:
$post
                
GET:
$get
                
TEXT;
        file_put_contents($file, $text);
    }

}
