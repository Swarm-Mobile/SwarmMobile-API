<?php

return [
    'oauth'          => [
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'swarmproduction-ivan.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
        'login'      => 'dashboard_admin',
        'password'   => 'Sp!swa5u',
        'database'   => 'oauth',
        'prefix'     => '',
    ],
    'ee'             => [
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'swarmproduction-ivan.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
        'login'      => 'dashboard_admin',
        'password'   => 'Sp!swa5u',
        'database'   => 'ee_prod',
        'prefix'     => '',
    ],
    'pos'            => [
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'swarmpos-ivan.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
        'login'      => 'posadmin',
        'password'   => 'dUdEph94aR5fr6',
        'database'   => 'pos_production',
        'prefix'     => '',
    ],
    'swarmdata'      => [
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'swarmdata-ivan.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
        'login'      => 'swarmdata',
        'password'   => '4Ha2Rap4ePHe',
        'database'   => 'swarmdata',
        'prefix'     => '',
    ],
    'backstage'      => [
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'swarmproduction-ivan.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
        'login'      => 'dashboard_admin',
        'password'   => 'Sp!swa5u',
        'database'   => 'swarm_backstage',
        'prefix'     => '',
    ],
    'portal'         => [
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'swarmpos-ivan.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
        'login'      => 'posadmin',
        'password'   => 'dUdEph94aR5fr6',
        'database'   => 'portal',
        'prefix'     => '',
    ],
    'rollups'        => [
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'swarmpos-ivan.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
        'login'      => 'posadmin',
        'password'   => 'dUdEph94aR5fr6',
        'database'   => 'rollups',
        'prefix'     => '',
    ],
    'pingAsPresence' => [
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'swarmpos-ivan.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
        'login'      => 'posadmin',
        'password'   => 'dUdEph94aR5fr6',
        'database'   => 'ping_as_presence',
        'prefix'     => '',
    ],
];