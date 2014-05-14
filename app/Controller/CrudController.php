<?php

App::uses('OAuth.OAuthComponent', 'OAuth.Controller/Component');

class CrudController extends AppController {

    public $view_fields = array();
    public $edit_fields = array();
    public $edit_tables = array();
    public $model = '';
    public $ctrl = '';

    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        $ctrl = strtolower(str_replace('Controller', '', get_class($this)));
        $this->ctrl = $ctrl;
        $this->layout = 'admin';
    }

    public function view() {
        $this->set('model', $this->model);
        $this->set('ctrl', $this->ctrl);
        $this->set('fields', $this->view_fields);        
        $this->set('data', $this->{$this->model}->find('all'));
        $this->render('/Crud/table');
    }

    public function add() {
        $this->set('model', $this->model);
        $this->set('ctrl', $this->ctrl);
        $this->set('fields', $this->edit_fields);
        $this->render('/Crud/form');
    }

    public function edit() {
        $this->set('model', $this->model);
        $this->set('ctrl', $this->ctrl);
        $this->set('fields', $this->edit_fields);
        $this->set('tables', $this->edit_tables);
        $id = $this->request->query['id'];
        $data = $this->{$this->model}->find('first', array('conditions' => array('id' => $id)));
        if (empty($data)) {
            $this->redirect(Router::url('/' . $this->ctrl . '/view'));
        }
        $this->set('data', $data);
        $this->render('/Crud/form');
    }

    public function delete() {
        $id = $this->request->query['id'];
        $this->{$this->model}->delete($id);
        $this->redirect(Router::url('/' . $this->ctrl . '/view'));
    }

    public function save() {
        foreach ($this->edit_fields as $model => $fields) {
            $data = array();
            $fields = array();
            $kId = (isset($this->data[$model]['id'])) ? 'id' : strtolower($model) . '_id';
            $id = array('k' => $kId, 'v' => $this->data[$model][$kId]);
            foreach ($this->data as $k => $v) {
                if (strpos($k, $model) === 0) {
                    $k = str_replace($model . '_', '', $k);
                    $data[$k] = $v;
                    $fields[] = $k;
                }
            }
            $oPrev = $this->$model->find('first', array('conditions' => array($id['k'] => $id['v'])));
            if (!empty($oPrev)) {
                $this->$model->id = $id['v'];
                foreach ($data as $k => $v) {
                    if ($k != $id['k']) {
                        if ($k == 'password') {
                            $v = trim($v);
                            if (empty($v)) {
                                continue;
                            }
                        }
                        $this->$model->saveField($k, $v);
                    }
                }
            } else {
                $pk = ($this->model->primaryKey != null) ? $this->model->primaryKey : 'id';
                $fk = strtolower($this->model . '_' . $pk);
                if ($model != $this->model) {
                    $data[$fk] = $this->data[$this->model . '_' . $pk];
                    $fields[] = $fk;
                    if (empty($data[$fk])) {
                        $data[$fk] = $this->{$this->model}->getInsertID();
                        unset($data[strtolower($model) . '_' . $pk]);
                    }
                }
                $this->$model->save($data, true, $fields);
                if ($model == 'Client') {
                    $this->$model->saveField('client_secret', Client::newClientSecret());
                }
            }
        }
        $this->redirect(Router::url('/' . $this->ctrl . '/view'));
    }

}
