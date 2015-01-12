<?php

class DATABASE_CONFIG
{

    public $default        = [];
    public $ee             = [];
    public $swarmdata      = [];
    public $pos            = [];
    public $rollups        = [];
    public $oauth          = [];
    public $backstage      = [];
    public $portal         = [];
    public $pingAsPresence = [];

    function __construct ()
    {
        $env    = getenv('server_location');
        $file   = __DIR__ . '/DB/' . (file_exists(__DIR__ . '/DB/' . $env . '.php') ? $env .'.php' : 'local.php');
        $config = include($file);        
        foreach ($config as $db => $dsn) {            
            $this->$db = $dsn;
        }
        $this->default = $config['ee'];
    }

}
