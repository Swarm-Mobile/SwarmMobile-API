<?php

class DATABASE_CONFIG {

    public $default = array();
    public $ee = array();
    public $swarmdata = array();
    public $swarmdataRead = array();
    public $pos = array();
    public $mongodb = array();
    public $consumerAPI = array();
    public $oauth = array();
    public $backstage = array();
    public $portal = array();
    public $local = array(
        'consumerAPI' => array(
            'datasource' => 'Mongodb.MongodbSource',
            'host' => 'ec2-50-18-84-202.us-west-1.compute.amazonaws.com',
            'database' => 'Swarm_BI_POS',
            'port' => 27017,
            'prefix' => '',
            'persistent' => true
        ),
        'mongodb' => array(
            'datasource' => 'Mongodb.MongodbSource',
            'host' => 'ec2-50-18-84-202.us-west-1.compute.amazonaws.com',
            'database' => 'API',
            'port' => 27017,
            'prefix' => '',
            'persistent' => true
        ),
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
        'consumerAPI' => array(
            'datasource' => 'Mongodb.MongodbSource',
            'host' => 'ec2-50-18-84-202.us-west-1.compute.amazonaws.com',
            'database' => 'Swarm_BI_POS',
            'port' => 27017,
            'prefix' => '',
            'persistent' => true
        ),
        'mongodb' => array(
            'datasource' => 'Mongodb.MongodbSource',
            'host' => 'ec2-50-18-84-202.us-west-1.compute.amazonaws.com',
            'database' => 'API',
            'port' => 27017,
            'prefix' => '',
            'persistent' => true
        ),
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
        'consumerAPI' => array(
            'datasource' => 'Mongodb.MongodbSource',
            'host' => '54.241.21.4',
            'database' => 'Swarm_BI_POS',
            'port' => 27017,
            'prefix' => '',
            'persistent' => true
        ),
        'mongodb' => array(
            'datasource' => 'Mongodb.MongodbSource',
            'host' => '54.241.21.4',
            'login' => 'mongouser',
            'password' => 'Swarmap!',
            'database' => 'API',
            'port' => 27017,
            'prefix' => '',
            'persistent' => true
        ),
        'oauth' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarmproduction.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'live_admin',
            'password' => 'TH9DrAqe4rAsta',
            'database' => 'oauth',
            'prefix' => '',
        ),
        'ee' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => true,
            'host' => 'swarmproduction.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'live_admin',
            'password' => 'TH9DrAqe4rAsta',
            'database' => 'ee_prod',
            'prefix' => '',
        ),
//        'pos' => array(
//            'datasource' => 'Database/Mysql',
//            'persistent' => true,
//            'host' => 'swarm-int.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
//            'login' => 'swarmdev',
//            'password' => 'dev2DaMax',
//            'database' => 'pos_test',
//            'prefix' => '',
//        ),
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
            'login' => 'live_admin',
            'password' => 'TH9DrAqe4rAsta',
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

    function __construct() {
        $env = getenv('server_location');
        $dbs = array(
            'ee',
            'swarmdata',
            'swarmdataRead',
            'pos',
            'mongodb',
            'oauth',
            'backstage',
            'consumerAPI',
            'portal',
        );
        $env = ((!empty($env) && isset($this->$env)) ? $env : 'local');
        foreach ($dbs as $dbname) {
            $this->$dbname = $this->{$env}[$dbname];
        }
        $this->default = $this->{$env}['ee'];
    }

}
