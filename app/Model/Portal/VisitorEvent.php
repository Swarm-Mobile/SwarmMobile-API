<?php

App::uses('AppModel', 'Model');

class VisitorEvent extends AppModel
{

    public $useDbConfig = 'portal';
    public $useTable    = 'visitorEvent';
    public $primaryKey  = 'id';
    public $id          = 'id';

}
