<?php

return [
    'oauth'          => [
        'datasource' => 'Database/Sqlite',
        'persistent' => false,
        'database'   => ':memory:',        
    ],
    'ee'             => [
        'datasource' => 'Database/Sqlite',
        'persistent' => false,
        'database'   => ':memory:',   
    ],
    'pos'            => [
        'datasource' => 'Database/Sqlite',
        'persistent' => false,
        'database'   => ':memory:',   
    ],
    'swarmdata'      => [
        'datasource' => 'Database/Sqlite',
        'persistent' => false,
        'database'   => ':memory:',   
    ],
    'backstage'      => [
        'datasource' => 'Database/Sqlite',
        'persistent' => false,
        'database'   => ':memory:',   
    ],
    'portal'         => [
        'datasource' => 'Database/Sqlite',
        'persistent' => false,
        'database'   => ':memory:',   
    ],
    'rollups'        => [
        'datasource' => 'Database/Sqlite',
        'persistent' => false,
        'database'   => ':memory:',   
    ],
    'pingAsPresence' => [
        'datasource' => 'Database/Sqlite',
        'persistent' => false,
        'database'   => ':memory:',   
    ],
];
