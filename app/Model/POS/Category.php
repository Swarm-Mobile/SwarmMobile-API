<?php

App::uses('AppModel', 'Model');

class Category extends AppModel
{

    public $useDbConfig = 'pos';
    public $useTable    = 'categories';
    public $primaryKey  = 'category_id';
    public $id          = 'category_id';

}
