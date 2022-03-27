<?php
use Slim\Container;


return [
    'settings' => [
        'displayErrorDetails' => true,
        "auth_service" => "http://api.authentification.local",
        "events_service" => "http://api.events.local",

        'dbfile' => __DIR__ . '/events.db.conf.ini.dist',  //! pourquoi ça ?? et pourquoi les deux fichiers de config des bases de données.

        'debug.name' => 'lbs.log',
        'debug.log' => __DIR__ . '/../log/debug.log',
        'debug.level' => \Monolog\Logger::DEBUG, 

        'warn.name' => 'lbs.log',
        'warn.log' => __DIR__ . '/../log/warn.log',
        'warn.level' => \Monolog\Logger::WARNING,

        'error.name' => 'lbs.log',               //Nom du log    
        'error.log' => __DIR__ . '/../log/error.log',  //* Nom du fichier du log    
        'error.level' => \Monolog\Logger::ERROR,  

        'secret' => 'b5a533c6d0ae5707'
    ]
];
