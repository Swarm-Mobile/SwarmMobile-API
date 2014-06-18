<?php

App::uses('APIComponent', 'Controller/Component');

class PortalComponent extends APIComponent {

    public function visitorEvent($params){
        if(!empty($_POST)){
            $file = realpath(__DIR__.'/../../../../').'/raw/'.date('Y_m_d_h_i_s_').uniqid();
            $content = var_export($_POST,true);
            file_put_contents($file, $content);
        }
        return array('path'=>$file);
    }
}
