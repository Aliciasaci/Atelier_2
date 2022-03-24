<?php
//Routes de l'API

use \reu\backoffice\app\controller\BackofficeController;
use \reu\backoffice\app\middleware\Middleware;
use \reu\backoffice\app\middleware\Token;


$app->post('/events[/]',BackofficeController::class. ':deleteEvent')->setName('deleteEvent')->add(middleware::class. ':putIntoJson');

$app->post('/users[/]',BackofficeController::class. ':deleteUser')->setName('deleteUser')->add(Middleware::class.':putIntoJson');

$app->post('/auth[/]',BackofficeController::class. ':auth')->setName('auth')->add(Middleware::class.':putIntoJson');

$app->post('/signin[/]',BackofficeController::class. ':signIn')->setName('signIn')->add(Middleware::class.':putIntoJson');

$app->get('/events/creators/{id}[/]',BackofficeController::class. ':getEvent')->setName('getEvent')->add(Middleware::class.':putIntoJson');
