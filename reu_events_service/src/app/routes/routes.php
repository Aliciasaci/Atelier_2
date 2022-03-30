<?php
//Routes de l'API

use \reu\events\app\controller\EventController;
use \reu\events\app\controller\CommentController;
use \reu\events\app\middleware\Middleware;
use \reu\events\app\middleware\EventValidator;
use \reu\events\app\middleware\Token;
use \DavidePastore\Slim\Validation\Validation as Validation ;

/**
 * VALIDATORS
 */
$eventsV = EventValidator::events_validators();
$commentsV = EventValidator::comments_validators();

/**
 * ROUTES DES EVENTS
//  */
$app->post('/events[/]',EventController::class. ':insertEvent')->setName('insertEvent')->add(Middleware::class. ':putIntoJson')->add(new Validation($eventsV));

$app->get('/events[/]',EventController::class. ':getAllEvents')->setName('getAllEvents')->add(Middleware::class. ':putIntoJson');

$app->get('/events/{id}[/]',EventController::class. ':getEvent')->setName('getEvent')->add(Middleware::class. ':putIntoJson');

$app->put('/events/{id}[/]',EventController::class. ':putEvent')->setName('putEvent')->add(Middleware::class. ':putIntoJson')->add(new Validation($eventsV));

$app->delete('/events[/]', EventController::class.':deleteEventsExpired')->setName('deleteEventsExpired')->add(Middleware::class. ':putIntoJson');

$app->delete('/events/{id}[/]', EventController::class.':deleteEventById')->setName('deleteEventById')->add(Middleware::class. ':putIntoJson');

$app->get('/events/creators/{id}[/]',EventController::class. ':getEventByIdCreator')->setName('getEventByIdCreator')->add(Middleware::class. ':putIntoJson');


/**
 * ROUTES DES COMMENTS
 */
$app->get('/events/{id}/comments[/]',CommentController::class. ':getCommentsOfEvent')->setName('getCommentsOfEvent')->add(Middleware::class. ':putIntoJson');
     
$app->post('/comments[/]',CommentController::class.':createComment')->setName('createComment')->add(Middleware::class.':putIntoJson');