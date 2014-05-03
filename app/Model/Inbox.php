<?php

App::uses('AppModel', 'Model');

class Inbox extends AppModel {

    public $useDbConfig = 'oauth';
    public $useTable    = 'inbox';
    
}
