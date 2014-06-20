<?php

/**
 *
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Database configuration class.
 *
 * You can specify multiple configurations for production, development and testing.
 *
 * datasource => The name of a supported datasource; valid options are as follows:
 *  Database/Mysql - MySQL 4 & 5,
 *  Database/Sqlite - SQLite (PHP5 only),
 *  Database/Postgres - PostgreSQL 7 and higher,
 *  Database/Sqlserver - Microsoft SQL Server 2005 and higher
 *
 * You can add custom database datasources (or override existing datasources) by adding the
 * appropriate file to app/Model/Datasource/Database. Datasources should be named 'MyDatasource.php',
 *
 *
 * persistent => true / false
 * Determines whether or not the database should use a persistent connection
 *
 * host =>
 * the host you connect to the database. To add a socket or port number, use 'port' => #
 *
 * prefix =>
 * Uses the given prefix for all the tables in this database. This setting can be overridden
 * on a per-table basis with the Model::$tablePrefix property.
 *
 * schema =>
 * For Postgres/Sqlserver specifies which schema you would like to use the tables in.
 * Postgres defaults to 'public'. For Sqlserver, it defaults to empty and use
 * the connected user's default schema (typically 'dbo').
 *
 * encoding =>
 * For MySQL, Postgres specifies the character encoding to use when connecting to the
 * database. Uses database default not specified.
 *
 * unix_socket =>
 * For MySQL to connect via socket specify the `unix_socket` parameter instead of `host` and `port`
 *
 * settings =>
 * Array of key/value pairs, on connection it executes SET statements for each pair
 * For MySQL : http://dev.mysql.com/doc/refman/5.6/en/set-statement.html
 * For Postgres : http://www.postgresql.org/docs/9.2/static/sql-set.html
 * For Sql Server : http://msdn.microsoft.com/en-us/library/ms190356.aspx
 */
class DATABASE_CONFIG {

    public $default = array();
    public $ee = array();
    public $swarmdata = array();
    public $swarmdataRead = array();
    public $pos = array();
    public $mongodb = array();
    public $consumerAPI = array();
    public $oauth = array();
    public $local = array(
        'consumerAPI' => array(
            'datasource' => 'Mongodb.MongodbSource',
            'host' => 'ec2-50-18-84-202.us-west-1.compute.amazonaws.com',
            'database' => 'Swarm_BI_POS',
            'port' => 27017,
            'prefix' => '',
            'persistent' => 'true'
        ),
        'mongodb' => array(
            'datasource' => 'Mongodb.MongodbSource',
            'host' => 'ec2-50-18-84-202.us-west-1.compute.amazonaws.com',
            'database' => 'API',
            'port' => 27017,
            'prefix' => '',
            'persistent' => 'true'
        ),
        'oauth' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'localhost',
            'login' => 'root',
            'password' => '',
            'database' => 'oauth',
            'prefix' => '',
        ),
        'ee' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'localhost',
            'login' => 'root',
            'password' => '',
            'database' => 'dev_db3',
            'prefix' => '',
        ),
        'pos' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'localhost',
            'login' => 'root',
            'password' => '',
            'database' => 'pos_production',
            'prefix' => '',
        ),        
        'swarmdata' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'localhost',
            'login' => 'root',
            'password' => '',
            'database' => 'swarmdata',
            'prefix' => '',
        ),
        'swarmdataRead' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'swarmdata-read.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdata',
            'password' => '4Ha2Rap4ePHe',
            'database' => 'swarmdata',
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
            'persistent' => 'true'
        ),
        'mongodb' => array(
            'datasource' => 'Mongodb.MongodbSource',
            'host' => 'ec2-50-18-84-202.us-west-1.compute.amazonaws.com',
            'database' => 'API',
            'port' => 27017,
            'prefix' => '',
            'persistent' => 'true'
        ),
        'oauth' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'swarm-int.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdev',
            'password' => 'dev2DaMax',
            'database' => 'oauth',
            'prefix' => '',
        ),
        'ee' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'swarm-int.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdev',
            'password' => 'dev2DaMax',
            'database' => 'ee_int',
            'prefix' => '',
        ),
        'pos' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'swarm-int.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdev',
            'password' => 'dev2DaMax',
            'database' => 'pos_int',
            'prefix' => '',
        ),
        'swarmdata' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'swarm-int.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdev',
            'password' => 'dev2DaMax',
            'database' => 'swarmdata_int',
            'prefix' => '',
        ),
        'swarmdataRead' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'swarmdata-read.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdata',
            'password' => '4Ha2Rap4ePHe',
            'database' => 'swarmdata',
            'prefix' => '',
        )
    );
    public $intjinesh = array(
        'consumerAPI' => array(
            'datasource' => 'Mongodb.MongodbSource',
            'host' => 'ec2-50-18-84-202.us-west-1.compute.amazonaws.com',
            'database' => 'Swarm_BI_POS',
            'port' => 27017,
            'prefix' => '',
            'persistent' => 'true'
        ),
        'mongodb' => array(
            'datasource' => 'Mongodb.MongodbSource',
            'host' => 'ec2-50-18-84-202.us-west-1.compute.amazonaws.com',
            'database' => 'API',
            'port' => 27017,
            'prefix' => '',
            'persistent' => 'true'
        ),
        'oauth' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'swarm-int.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdev',
            'password' => 'dev2DaMax',
            'database' => 'oauth',
            'prefix' => '',
        ),
        'ee' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'swarm-int.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdev',
            'password' => 'dev2DaMax',
            'database' => 'ee_jinesh',
            'prefix' => '',
        ),
        'pos' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'swarm-int.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdev',
            'password' => 'dev2DaMax',
            'database' => 'pos_int',
            'prefix' => '',
        ),
        'swarmdata' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'swarm-int.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdev',
            'password' => 'dev2DaMax',
            'database' => 'swarmdata_int',
            'prefix' => '',
        ),
        'swarmdataRead' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'swarmdata-read.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdata',
            'password' => '4Ha2Rap4ePHe',
            'database' => 'swarmdata',
            'prefix' => '',
        )
    );
    public $live = array(
        'consumerAPI' => array(
            'datasource' => 'Mongodb.MongodbSource',
            'host' => 'ec2-50-18-84-202.us-west-1.compute.amazonaws.com',
            'database' => 'Swarm_BI_POS',
            'port' => 27017,
            'prefix' => '',
            'persistent' => 'true'
        ),
        'mongodb' => array(
            'datasource' => 'Mongodb.MongodbSource',
            'host' => 'mongo.swarm-mobile.com',
            'login' => 'mongouser',
            'password' => 'Swarmap!',
            'database' => 'API',
            'port' => 27017,
            'prefix' => '',
            'persistent' => 'true'
        ),
        'oauth' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'swarmproduction.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'live_admin',
            'password' => 'TH9DrAqe4rAsta',
            'database' => 'oauth',
            'prefix' => '',
        ),
        'ee' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'swarmproduction.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'live_admin',
            'password' => 'TH9DrAqe4rAsta',
            'database' => 'ee_prod',
            'prefix' => '',
        ),
        'pos' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'swarmposdata.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'posadmin',
            'password' => 'dUdEph94aR5fr6',
            'database' => 'pos_production',
            'prefix' => '',
        ),
        'swarmdata' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'swarmdata.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdata',
            'password' => '4Ha2Rap4ePHe',
            'database' => 'swarmdata',
            'prefix' => '',
        ),
        'swarmdataRead' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'swarmdata-read.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdata',
            'password' => '4Ha2Rap4ePHe',
            'database' => 'swarmdata',
            'prefix' => '',
        ),
        'portal' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'swarm-device-data.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarm_admin',
            'password' => '+uPaSeQeru5a',
            'database' => 'portal',
            'prefix' => '',
        )
    );
    public $dev_yaron = array(
        'consumerAPI' => array(
            'datasource' => 'Mongodb.MongodbSource',
            'host' => 'ec2-50-18-84-202.us-west-1.compute.amazonaws.com',
            'database' => 'Swarm_BI_POS',
            'port' => 27017,
            'prefix' => '',
            'persistent' => 'true'
        ),
        'mongodb' => array(
            'datasource' => 'Mongodb.MongodbSource',
            'host' => 'ec2-50-18-84-202.us-west-1.compute.amazonaws.com',
            'database' => 'API',
            'port' => 27017,
            'prefix' => '',
            'persistent' => 'true'
        ),
        'oauth' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'swarm-int.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdev',
            'password' => 'dev2DaMax',
            'database' => 'oauth',
            'prefix' => '',
        ),
        'ee' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'localhost',
            'login' => 'swarm_admin',
            'password' => 'TH9DrAqe4rAsta',
            'database' => 'swarm_dev_db3',
            'prefix' => '',
        ),
        'pos' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'swarm-int.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdev',
            'password' => 'dev2DaMax',
            'database' => 'pos_int',
            'prefix' => '',
        ),
        'swarmdata' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'swarm-int.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdev',
            'password' => 'dev2DaMax',
            'database' => 'swarmdata_int',
            'prefix' => '',
        ),
        'swarmdataRead' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'swarmdata-read.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
            'login' => 'swarmdata',
            'password' => '4Ha2Rap4ePHe',
            'database' => 'swarmdata',
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
            'consumerAPI'
        );
        $env = ((!empty($env) && isset($this->$env)) ? $env : 'local');
        foreach ($dbs as $dbname) {
            $this->$dbname = $this->{$env}[$dbname];
        }
        $this->default = $this->{$env}['ee'];
    }

}
