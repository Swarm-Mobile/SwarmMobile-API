<?php

App::uses('UserModel'       , 'Model');
App::uses('OAuth.Client'    , 'Model');
App::uses('CrudController'  , 'Controller');

class UsersController extends CrudController {
    var $uses = array('User', 'Client');
    
    public $view_fields = array(
        'User' => array('id', 'username'),
        'Client' => array('client_id', 'client_secret', 'redirect_uri'),
    );
    public $edit_fields = array(
        'User' => array('username', 'password'),
        'Client' => array('redirect_uri'),
    );
    public $edit_tables = array(
        'AuthCode' => array('code', 'redirect_uri', 'expires', 'scope'),
        'RefreshToken' => array('refresh_token', 'expires', 'scope'),
        'AccessToken' => array('oauth_token', 'expires', 'scope'),
    );
    public $model = 'User';

    public function edit() {
        $this->set('model', $this->model);
        $this->set('ctrl', $this->ctrl);
        $this->set('fields', $this->edit_fields);
        $this->set('tables', $this->edit_tables);
        $id             = $this->request->query['id'];
        $data           = $this->{$this->model}->find('first', array('conditions' => array('id' => $id)));
        $tables_data    = $this->Client->find('first', array('conditions' => array('client_id' => $data['Client']['client_id'])));
        if (empty($data)) {
            $this->redirect(Router::url('/' . $this->ctrl . '/view'));
        }
        $this->set('data', $data);
        $this->set('tables_data', $tables_data);
        $this->render('/Crud/form');
    }

}
