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
$app->post('/events[/]',EventController::class. ':insertEvent')->setName('insertEvent')->add(middleware::class. ':putIntoJson')->add(new Validation($eventsV));

$app->get('/events[/]',EventController::class. ':getAllEvents')->setName('getAllEvents')->add(middleware::class. ':putIntoJson');

$app->get('/events/{id}[/]',EventController::class. ':getEvent')->setName('getEvent')->add(middleware::class. ':putIntoJson');

$app->put('/events/{id}[/]',EventController::class. ':putEvent')->setName('putEvent')->add(middleware::class. ':putIntoJson')->add(new Validation($eventsV));

$app->delete('/events[/]', EventController::class.':deleteEventsExpired')->setName('deleteEventsExpired')->add(middleware::class. ':putIntoJson');

$app->delete('/events/{id}[/]', EventController::class.':deleteEventById')->setName('deleteEventById')->add(middleware::class. ':putIntoJson');

$app->get('/events/creators/{id}[/]',EventController::class. ':getEventByIdCreator')->setName('getEventByIdCreator')->add(middleware::class. ':putIntoJson');


/**
 * ROUTES DES COMMENTS
 */
$app->get('/comments/{idEvent}[/]',CommentController::class. ':getCommentsFromEvent')->setName('getCommentsFromEvent')->add(middleware::class. ':putIntoJson')->add(new Validation($commentsV));
     
$app->post('/comments[/]',CommentController::class.':create')->setName('createComment')->add(middleware::class.':putIntoJson')->add(new Validation($commentsV));