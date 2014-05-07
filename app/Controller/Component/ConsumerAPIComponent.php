<?php

require_once 'DBComponent.php';

class ConsumerAPIComponent extends APIComponent{
    
    public $paths = array();
    
    public function __call($name, $arguments) {
        
    }

}
