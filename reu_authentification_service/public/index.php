<?php

use reu\authentification\app\middlewares\Middleware;
use reu\authentification\app\bootstrap\ReuBootstrap;

//Dépendances 
require_once  __DIR__ . '/../src/vendor/autoload.php';
$config = require_once __DIR__ . '/../src/app/conf/settings.php';
$dependencies = require_once __DIR__ . '/../src/app/conf/dependencies.php';
$errors = require_once __DIR__ . '/../src/app/conf/errors.php';


$c = new \Slim\Container(array_merge($config,$dependencies,$errors));
$app = new \Slim\App($c);
ReuBootstrap::startEloquent($c->settings['dbfile']);

//Les routes de l'applications
require_once __DIR__ . '/../src/app/routes/routes.php';

//Ajouter les 2 middlewares putIntoJson à toute l'appli
$app->add(middleware::class.':putIntoJson');
$app->run();











