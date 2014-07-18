<?php

class DATABASE_CONFIG {

    public $default = array();
    public $ee = array();
    public $swarmdata = array();
    public $swarmdataRead = array();
    public $pos = array();
    public $rollups = array();    
    public $oauth = array();
    public $backstage = array();
    public $portal = array();
    public $local = array(
        'oauth' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'localhost',
            'login' => 'root',
            'password' => '',
            'database' => 'oauth',
            'prefix' => '',
        ),
        'ee' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'localhost',
            'login' => 'root',
            'password' => '',
            'database' => 'dev_db3',
            'prefix' => '',
        ),
        'pos' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'localhost',
            'login' => 'root',
            'password' => '',
            'database' => 'pos_production',
            'prefix' => '',
        ),
        'swarmdata' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'localhost',
            'login' => 'root',
            'password' => '',
            'database' => 'swarmdata',
            'prefix' => '',
        ),
        'swarmdataRead' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'localhost',
            'login' => 'root',
            'password' => '',
            'database' => 'swarmdata',
            'prefix' => '',
        ),
        'backstage' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'localhost',
            'login' => 'root',
            'password' => '',
            'database' => 'swarm_backstage',
            'prefix' => '',
        ),
        'portal' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarm-device-data.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarm_admin',
            'password' => '+uPaSeQeru5a',
            'database' => 'portal',
            'prefix' => '',
        )
    );
    public $int = array(
        'oauth' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarm-int.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdev',
            'password' => 'dev2DaMax',
            'database' => 'oauth',
            'prefix' => '',
        ),
        'ee' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarm-int.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdev',
            'password' => 'dev2DaMax',
            'database' => 'ee_int',
            'prefix' => '',
        ),
        'pos' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarm-int.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdev',
            'password' => 'dev2DaMax',
            'database' => 'pos_int',
            'prefix' => '',
        ),
        'swarmdata' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarm-int.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdev',
            'password' => 'dev2DaMax',
            'database' => 'swarmdata_int',
            'prefix' => '',
        ),
        'swarmdataRead' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarm-int.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdev',
            'password' => 'dev2DaMax',
            'database' => 'swarmdata_int',
            'prefix' => '',
        ),
        'backstage' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarm-int.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdev',
            'password' => 'dev2DaMax',
            'database' => 'swarm_backstage',
            'prefix' => '',
        ),
        'portal' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarm-device-data.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarm_admin',
            'password' => '+uPaSeQeru5a',
            'database' => 'portal',
            'prefix' => '',
        )
    );
    public $live = array(
        'oauth' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarmproduction.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'dashboard_admin',
            'password' => 'Sp!swa5u',
            'database' => 'oauth',
            'prefix' => '',
        ),
        'ee' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarmproduction.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'dashboard_admin',
            'password' => 'Sp!swa5u',
            'database' => 'ee_prod',
            'prefix' => '',
        ),
        'pos' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarmposdata.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'posadmin',
            'password' => 'dUdEph94aR5fr6',
            'database' => 'pos_production',
            'prefix' => '',
        ),
        'swarmdata' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarmdata.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdata',
            'password' => '4Ha2Rap4ePHe',
            'database' => 'swarmdata',
            'prefix' => '',
        ),
        'swarmdataRead' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarmdata-read.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdata',
            'password' => '4Ha2Rap4ePHe',
            'database' => 'swarmdata',
            'prefix' => '',
        ),
        'backstage' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarmproduction.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'dashboard_admin',
            'password' => 'Sp!swa5u',
            'database' => 'swarm_backstage',
            'prefix' => '',
        ),
        'portal' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarm-device-data.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarm_admin',
            'password' => '+uPaSeQeru5a',
            'database' => 'portal',
            'prefix' => '',
        ),
        'rollups' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarm-rollups.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmrollups',
            'password' => 'f4uwrapR',
            'database' => 'rollups',
            'prefix' => '',
        )
    );
    public $hex = array(
        'oauth' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarmproduction.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'dashboard_admin',
            'password' => 'Sp!swa5u',
            'database' => 'oauth',
            'prefix' => '',
        ),
        'ee' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarmproduction.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'dashboard_admin',
            'password' => 'Sp!swa5u',
            'database' => 'ee_prod',
            'prefix' => '',
        ),
        'pos' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarmposdata.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'posadmin',
            'password' => 'dUdEph94aR5fr6',
            'database' => 'pos_production',
            'prefix' => '',
        ),
        'swarmdata' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarmdata.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdata',
            'password' => '4Ha2Rap4ePHe',
            'database' => 'swarmdata',
            'prefix' => '',
        ),
        'swarmdataRead' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarmdata-read.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdata',
            'password' => '4Ha2Rap4ePHe',
            'database' => 'swarmdata',
            'prefix' => '',
        ),
        'backstage' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarm-hex.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarm_ios',
            'password' => 'F6gatRad',
            'database' => 'swarm_backstage',
            'prefix' => '',
        ),
        'portal' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarm-device-data.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarm_admin',
            'password' => '+uPaSeQeru5a',
            'database' => 'portal',
            'prefix' => '',
        ),
        'rollups' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarm-hex.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarm_ios',
            'password' => 'F6gatRad',
            'database' => 'rollups',
            'prefix' => '',
        )
    );

    function __construct() {
        $env = getenv('server_location');
        $dbs = array(
            'ee',
            'swarmdata',
            'swarmdataRead',
            'pos',            
            'oauth',
            'backstage',            
            'rollups',            
            'portal',
        );
        $env = ((!empty($env) && isset($this->$env)) ? $env : 'local');
        foreach ($dbs as $dbname) {
            $this->$dbname = $this->{$env}[$dbname];
        }
        $this->default = $this->{$env}['ee'];
    }

}
