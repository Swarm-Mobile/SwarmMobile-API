<?php

App::uses('AppModel', 'Model');
App::uses('CrudController'  , 'Controller');

class InboxController extends CrudController{
    
    var $uses = array('Inbox');
    
    public $view_fields = array(
        'Inbox' => array('id', 'username', 'redirect_uri', 'status', 'ts')        
    );
    public $edit_fields = array(
        'Inbox' => array('username', 'redirect_uri', 'status', 'description')        
    );
    
    public $model = 'Inbox';
    
}
