<?php

use \reu\authentification\app\controller\REUAuthController;
use \reu\authentification\app\middlewares\Middleware;
use \DavidePastore\Slim\Validation\Validation as Validation;
use \reu\authentification\app\controller\ParticipationController;



$validators = Middleware::user_validators();

$app->post('/auth[/]',REUAuthController::class.':authenticate')->setName('authenticate');

$app->post('/create[/]',REUAuthController::class.':create')->setName('create');

$app->delete('/users[/]',REUAuthController::class.':delete')->setName('delete');

$app->post('/participations[/]',ParticipationController::class.':createPar')->setName('createPar');

$app->get('/events/participations/{id}[/]',ParticipationController::class. ':getParByIdEvent')->setName('getParByIdEvent');

$app->get('/events/non_participations/{id}[/]',ParticipationController::class. ':getNonParByIdEvent')->setName('getNonParByIdEvent');

$app->get('/users/{id}/invitations/[/]',ParticipationController::class. ':getInvitationsByUserId')->setName('getInvitationsByUserId');

$app->put('/invitations/{id}[/]',ParticipationController::class. ':updateInvitation')->setName('updateInvitation');
