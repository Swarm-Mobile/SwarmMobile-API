<?php

App::uses('AppModel', 'Model');

class Partner extends AppModel
{

    public $useDbConfig  = 'backstage';
    public $useTable     = 'partner';
    public $displayField = 'name';
    public $id           = 'id';

}
