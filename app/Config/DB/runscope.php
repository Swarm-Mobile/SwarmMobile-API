<?php

return [
    'oauth'          => [
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'staging-all.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
        'login'      => 'swarmdata',
        'password'   => '4Ha2Rap4ePHe',
        'database'   => 'oauth',
        'prefix'     => '',
    ],
    'ee'             => [
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'staging-all.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
        'login'      => 'swarmdata',
        'password'   => '4Ha2Rap4ePHe',
        'database'   => 'ee_prod',
        'prefix'     => '',
    ],
    'pos'            => [
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'staging-all.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
        'login'      => 'swarmdata',
        'password'   => '4Ha2Rap4ePHe',
        'database'   => 'pos_production',
        'prefix'     => '',
    ],
    'swarmdata'      => [
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'staging-all.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
        'login'      => 'swarmdata',
        'password'   => '4Ha2Rap4ePHe',
        'database'   => 'swarmdata',
        'prefix'     => '',
    ],
    'backstage'      => [
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'staging-all.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
        'login'      => 'swarmdata',
        'password'   => '4Ha2Rap4ePHe',
        'database'   => 'swarm_backstage',
        'prefix'     => '',
    ],
    'portal'         => [
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'staging-all.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
        'login'      => 'swarmdata',
        'password'   => '4Ha2Rap4ePHe',
        'database'   => 'portal',
        'prefix'     => '',
    ],
    'rollups'        => [
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'staging-all.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
        'login'      => 'swarmdata',
        'password'   => '4Ha2Rap4ePHe',
        'database'   => 'rollups',
        'prefix'     => '',
    ],
    'pingAsPresence' => [
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'staging-all.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
        'login'      => 'swarmdata',
        'password'   => '4Ha2Rap4ePHe',
        'database'   => 'ping_as_presence',
        'prefix'     => '',
    ],
];