<?php
//Routes de l'API

use \reu\backoffice\app\controller\BackofficeController;
use \reu\backoffice\app\middleware\Middleware;
use \reu\backoffice\app\middleware\Token;


$app->delete('/events[/]',BackofficeController::class. ':deleteEvent')->setName('deleteEvent')->add(middleware::class. ':putIntoJson');

$app->delete('/users[/]',BackofficeController::class. ':deleteUser')->setName('deleteUser')->add(Middleware::class.':putIntoJson');

$app->get('/auth[/]',BackofficeController::class. ':auth')->setName('auth')->add(Middleware::class.':putIntoJson');