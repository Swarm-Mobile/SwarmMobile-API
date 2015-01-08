<?php

App::import('Lib', 'JsonResponse');
App::uses('Controller', 'Controller');
App::uses('SwarmErrorCodes', 'Controller/Component');
App::uses('AppModel', 'Model');

class AppController extends Controller
{

    protected $_responseClass = 'JsonResponse';

}
