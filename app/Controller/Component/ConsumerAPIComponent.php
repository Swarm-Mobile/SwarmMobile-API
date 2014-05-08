<?php

require_once 'DBComponent.php';

class ConsumerAPIComponent extends APIComponent{

    public $paths = array(
        'Person' => array(
            'Purchase' => array(),
            'Product'  => array('Purchase'),
            'Location' => array('Purchase', 'Product'),
            'Brand'    => array('Purchase', 'Product'),
        ),
        'Purchase' => array(
            'Product'  => array(),
            'Location' => array('Product'),
            'Brand'    => array('Product'),
            'Person'   => array()
        ),
        'Product'  => array (
            'Location' => array(),
            'Brand'    => array(),
            'Person'   => array('Purchase'),
            'Purchase' => array()
        ),
        'Brand'  => array(
            'Person'    => array('Product', 'Purchase'),
            'Product'  => array(),
            'Location' => array()
        ),
        'Visit'  => array(
            'Person'   => array(),
            'Device'   => array(),
            'Location' => array()
        ),
        'Device' => array(
            'Person'  => array()
        ),
        'Location'  => array(
            'Person'   => array('Product', 'Purchase'),
            'Product'  => array(),
            'Purchase' => array('Product'),
            'Brand'    => array(),
            'Visit'    => array()
        )
    );
    
    public function __call($name, $arguments) {
        
    }
}
