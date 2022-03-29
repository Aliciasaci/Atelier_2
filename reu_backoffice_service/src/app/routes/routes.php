<?php
//Routes de l'API

use \reu\backoffice\app\controller\BackofficeController;
use \reu\backoffice\app\middleware\Middleware;
use \reu\backoffice\app\middleware\Token;

//S'authentifier
$app->post('/auth[/]',BackofficeController::class. ':auth')->setName('auth')->add(Middleware::class.':putIntoJson');

//S'inscrire
$app->post('/signin[/]',BackofficeController::class. ':signIn')->setName('signIn')->add(Middleware::class.':putIntoJson');

//get les events d'un certain user
$app->get('/events/creators/{id}[/]',BackofficeController::class. ':getEventByIdCreator')->setName('getEventByIdCreator')->add(Middleware::class.':putIntoJson')->add(Middleware::class. ':check');

//Get un certain event par id
$app->get('/events/{id}[/]',BackofficeController::class. ':getOneEvent')->setName('getOneEvent')->add(Middleware::class.':putIntoJson');  //!remettre la route du check ici

// Get un certain event par id 
$app->delete('/events/{id}[/]',BackofficeController::class. ':deleteEventById')->setName('deleteEventById')->add(Middleware::class.':putIntoJson')->add(Middleware::class. ':check');

//Get tous les events
$app->get('/events[/]',BackofficeController::class. ':getEvents')->setName('getEvents')->add(Middleware::class.':putIntoJson')->add(Middleware::class. ':check');

//Créer un event
$app->post('/events[/]',BackofficeController::class. ':createEvent')->setName('createEvent')->add(Middleware::class.':putIntoJson')->add(Middleware::class. ':check');

//Créer une participation
$app->post('/invitations[/]',BackofficeController::class. ':createInvitation')->setName('createInvitation')->add(Middleware::class.':putIntoJson')->add(Middleware::class. ':check');

//Get les users qui ont dit oui à un certain event par id
$app->get('/events/participations/{id}[/]',BackofficeController::class. ':getParByIdEvent')->setName('getParByIdEvent')->add(Middleware::class.':putIntoJson')->add(Middleware::class. ':check');

//Get les users qui ont dit non à un certain event par id
$app->get('/events/non_participations/{id}[/]',BackofficeController::class. ':getNonParByIdEvent')->setName('getNonParByIdEvent')->add(Middleware::class.':putIntoJson')->add(Middleware::class. ':check');

//Get les invitations d'un certain user par son id
$app->get('/users/{id}/invitations[/]',BackofficeController::class. ':getInvitationsByUserId')->setName('getInvitationsByUserId')->add(Middleware::class.':putIntoJson')->add(Middleware::class. ':check');

//Update une invitation avec une réponse (oui ou non)
$app->put('/invitations/{id}[/]',BackofficeController::class. ':updateInvitation')->setName('updateInvitation')->add(Middleware::class.':putIntoJson')->add(Middleware::class. ':check');

//Chercher un user par son username ou email
$app->post('/searches[/]',BackofficeController::class. ':searchUser')->setName('searchUser')->add(Middleware::class.':putIntoJson')->add(Middleware::class. ':check');

//Créer une instance de visiteur
//! on chèque pas de access token (puisqu'il n'existe pas encore)
$app->post('/visiteurs[/]',BackofficeController::class. ':createVisiteur')->setName('createVisiteur')->add(Middleware::class.':putIntoJson');

//Get les information d'un user par son Id
$app->get('/users/{id}[/]',BackofficeController::class. ':getUserInformations')->setName('getUserInformations')->add(Middleware::class.':putIntoJson');

//Update les inforamtions du profil d'un user
$app->put('/users/{id}/informations[/]',BackofficeController::class. ':updateUserInformations')->setName('updateUserInformations')->add(Middleware::class.':putIntoJson')->add(Middleware::class. ':check');

//Supprimer les events inactifs
$app->delete('/events[/]',BackofficeController::class. ':deleteEvent')->setName('deleteEvent')->add(middleware::class. ':putIntoJson')->add(Middleware::class. ':check');

//Supprimer les users inactifs
$app->delete('/users[/]',BackofficeController::class. ':deleteUser')->setName('deleteUser')->add(Middleware::class.':putIntoJson')->add(Middleware::class. ':check');
