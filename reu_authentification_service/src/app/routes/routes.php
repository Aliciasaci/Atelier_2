<?php

use \reu\authentification\app\controller\REUAuthController;
use \reu\authentification\app\middlewares\Middleware;
use \DavidePastore\Slim\Validation\Validation as Validation;


$validators = Middleware::user_validators();

$app->post('/auth[/]',REUAuthController::class.':authenticate')->setName('authenticate')->add(middleware::class.':putIntoJson');

$app->post('/create[/]',REUAuthController::class.':create')->setName('create')->add(middleware::class.':putIntoJson');

$app->delete('/users[/]',REUAuthController::class.':delete')->setName('delete')->add(middleware::class.':putIntoJson')->add(new Validation($validators));