<?php

require_once 'DBComponent.php';

class PersonComponent extends ConsumerAPIComponent {}
class DeviceComponent extends ConsumerAPIComponent {}
class VisitComponent extends ConsumerAPIComponent {}
class PurchaseComponent extends ConsumerAPIComponent {}
class ProductComponent extends ConsumerAPIComponent {}
class LocationComponent extends ConsumerAPIComponent {}
class BrandComponent extends ConsumerAPIComponent {}

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
        $start = str_replace('Component', '', get_class($this));
        $end = ucfirst($name);
        if (!isset($this->paths[$start][$end])) {
            throw new APIException(404, 'endpoint_not_found', "The requested reference method don't exists");
        } else {            
            return $this->search($start, $end, $arguments[0]);
        }
    }
    private function search($start, $end, $params) {
        $path = $this->paths[$start][$end];  
        $start = strtolower($start);
        $end = strtolower($end);
        $oModel = new Model(false, $end, 'consumerAPI');                
        $conditions = array();
        foreach($params['filter'] as $k=>$v){            
            $value = (is_numeric($v))?$v:new MongoRegex("/$v/i");        
            $conditions[$k] = $value;            
        }        
        $aRes = $oModel->find('all', array('conditions' => $conditions));
        $result = array();
        foreach($aRes as $oRow){            
            $id = $oRow['Model']['id'];
            $prev = $end;            
            while($collection = array_pop($path)){
                $cur = strtolower($collection);                
                $oModel->setSource($cur); 
                $conditions = array('_'.$prev => new MongoId($id));
                $aCollectionRes = $oModel->find('all', array(                    
                    'conditions' => $conditions
                ));
                $id = $aCollectionRes[0]['Model']['id'];
                $prev = $cur;                
            }
            $id = $aCollectionRes[0]['Model']['_'.$start];
            $oModel->setSource($start);
            $conditions = array('_id' => new MongoId($id));
            $tmp = $oModel->find('all', array('conditions' => $conditions));
            foreach($tmp as $element){
                $result[] = $tmp[0]['Model'];
            }            
        }        
        return $result;
    }
}
