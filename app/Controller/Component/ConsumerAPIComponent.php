<?php

require_once 'DBComponent.php';

class PersonComponent extends ConsumerAPIComponent{}
class DeviceComponent extends ConsumerAPIComponent{}
class VisitComponent extends ConsumerAPIComponent{}
class PurchaseComponent extends ConsumerAPIComponent{}
class ProductComponent extends ConsumerAPIComponent{}
class LocationComponent extends ConsumerAPIComponent{}
class BrandComponent extends ConsumerAPIComponent{}

class ConsumerAPIComponent extends APIComponent{
    
    public $paths = array(
		'Person' => array(
			'Purchases' => array(),
			'Products'  => array('Purchases'),
			'Locations' => array('Purchases', 'Products'),
			'Brands'    => array('Purchases', 'Products')
		),
		'Purchases' => array(
			'Products'  => array(),
			'Locations' => array('Products'),
			'Brands'    => array('Products'),
		),
		'Products'  => array (
			'Locations'	=> array(),
			'Brands'    => array(),
		),
		'Brands'  => array(
			'Person'    => array('Products', 'Purchases'),
			'Products'  => array(''),
			'Locations' => array('')
		),
		'Visits'  => array(
			'Person'    => array()
		),
		'Devices' => array(),
		
	
	);
    
    public function __call($name, $arguments) {
        var_dump($name, $arguments);
        throw new APIException(404, 'endpoint_not_found', "The requested reference method don't exists");
    }

}
