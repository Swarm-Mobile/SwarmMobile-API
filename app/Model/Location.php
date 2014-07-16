<?php

App::uses('AppModel', 'Model');

class Location extends AppModel {

    public $useDbConfig = 'backstage';
    public $useTable = 'location';
    public $displayField = 'name';
    public $id = 'id';
    public $hasAndBelongsToMany = array(
        "Setting" => array(
            'joinTable' => 'location_setting'
        )
    );
    public $validate = array(
        'name' => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
                'required' => true,
            ),
        ),
        'address1' => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
                'required' => true,
            ),
        ),
        'city' => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
                'required' => true,
            ),
        ),
        'zipcode' => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
                'required' => true,
            ),
        ),
        'country' => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
                'required' => true,
            ),
        )
    );

    public function locationExists($location_id) {
        if(empty($location_id)) return  true;
        $location = $this->find('first', array('conditions' => array('Location.id' => $location_id)));
        if(!empty($location)) return true;
        return false;
    }
    
    public function nameAddressCombinationExists($combination = '', $name = '') {
        $sSQL = <<<SQL
SELECT COUNT(*) as count FROM (
    SELECT GROUP_CONCAT(a.value SEPARATOR ' ') full_address
    FROM location l
    INNER JOIN 
    (
    SELECT location_id, value
    FROM location_setting ls
    WHERE ls.setting_id IN (1,3)    
    ) a
    ON a.location_id = l.id
    AND l.name ="$name"
    GROUP BY l.id
    HAVING full_address ="$combination"
) b
SQL;
         return (int) $this->query($sSQL)[0][0]['count'];
    }
}
