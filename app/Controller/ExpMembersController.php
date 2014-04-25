<?php

App::uses('AdminController', 'Controller');
App::uses('ExpMember', 'Model');

/**
 * ExpMembers Controller
 *
 * @property ExpMember $ExpMember
 * @property PaginatorComponent $Paginator
 * @property AuthComponent $Auth
 */
class ExpMembersController extends AdminController {

    public $helpers = array('Html', 'Session');

    public function login() {
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                return $this->redirect($this->Auth->redirect());
            }
            $this->Session->setFlash(__('Invalid username or password, try again'));
        }
    }

    public function logout() {
        return $this->redirect($this->Auth->logout());
    }

}
