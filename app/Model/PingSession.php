<?php

App::uses('Model', 'Model');
class PingSession extends Model {
    /**
     *
     * @var string
     */
    public $useTable = 'session';

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
    
    public $belongsTo   = array ("PingUser");
    
    public $hasMany     = array ("PingFootprint");
}