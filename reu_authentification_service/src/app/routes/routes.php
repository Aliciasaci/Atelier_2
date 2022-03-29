<?php

use \reu\authentification\app\controller\REUAuthController;
use \reu\authentification\app\middlewares\Middleware;
use \DavidePastore\Slim\Validation\Validation as Validation;
use \reu\authentification\app\controller\ParticipationController;

$validators = Middleware::user_validators();

//s'authentifier
$app->post('/auth[/]',REUAuthController::class.':authenticate')->setName('authenticate')->add(Middleware::class.':putIntoJson');

//Créer un nouveau user
$app->post('/create[/]',REUAuthController::class.':create')->setName('create')->add(Middleware::class.':putIntoJson');

//supprimer les users dont le compte dépasse 1 an d'inactivité
$app->delete('/users[/]',REUAuthController::class.':delete')->setName('delete')->add(Middleware::class.':putIntoJson');

//Créer une invitation
$app->post('/invitations[/]',ParticipationController::class.':createInvitation')->setName('createInvitation')->add(Middleware::class.':putIntoJson');

//Get toutes les participations positives à un certain évènement
$app->get('/events/participations/{id}[/]',ParticipationController::class. ':getParByIdEvent')->setName('getParByIdEvent')->add(Middleware::class.':putIntoJson');

//Get toutes les participations négative à un certain évènement
$app->get('/events/non_participations/{id}[/]',ParticipationController::class. ':getNonParByIdEvent')->setName('getNonParByIdEvent')->add(Middleware::class.':putIntoJson');

//Get toutes les évènement auquelles un user a été invité
$app->get('/users/{id}/invitations/[/]',ParticipationController::class. ':getInvitationsByUserId')->setName('getInvitationsByUserId')->add(Middleware::class.':putIntoJson');

//Modifier la réponse à une invitation pour y répondre par un oui ou pas un non 
$app->put('/invitations/{id}[/]',ParticipationController::class. ':updateInvitation')->setName('updateInvitation')->add(Middleware::class.':putIntoJson');

//rechercher un user par son username ou email
$app->post('/searches[/]',REUAuthController::class. ':searchUser')->setName('searchUser')->add(Middleware::class.':putIntoJson');

//Créer une instance de visiteur 
$app->post('/visiteurs[/]',REUAuthController::class. ':createVisiteur')->setName('createVisiteur')->add(Middleware::class.':putIntoJson');

//Get les informations d'un user
$app->get('/users/{id}[/]',REUAuthController::class. ':getUserInformations')->setName('getUserInformations')->add(Middleware::class.':putIntoJson');
