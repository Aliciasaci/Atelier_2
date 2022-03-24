<?php
require_once  __DIR__ . '/../src/vendor/autoload.php';
use reu\authentification\app\bootstrap\ReuBootstrap;
$config = require_once __DIR__ . '/../src/app/conf/settings.php';
$dependencies = require_once __DIR__ . '/../src/app/conf/dependencies.php';
$errors = require_once __DIR__ . '/../src/app/conf/errors.php';
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use reu\authentification\app\middlewares\Middleware;

$c = new \Slim\Container(array_merge($config,$dependencies,$errors));
$app = new \Slim\App($c);
ReuBootstrap::startEloquent($c->settings['dbfile']);

require_once __DIR__ . '/../src/app/routes/routes.php';

$app->run();











