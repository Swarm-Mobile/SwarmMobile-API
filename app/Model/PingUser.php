<?php

App::uses('AppModel', 'Model');
class PingUser extends Model {

    /**
     *
     * @var string
     */
    public $useTable = 'user';

    /**
     *
     * @var string
     */
    public $id = 'id';

    /**
     *
     * @var string
     */
    public $useDbConfig = 'pingAsPresence';
    
    public $hasMany     = array ("PingSession");
}