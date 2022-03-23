<?php

namespace reu\backoffice\app\controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use reu\backoffice\app\models\Event;
use reu\backoffice\app\models\User;
use reu\backoffice\app\errors\Writer;
use DateTime;
use Ramsey\Uuid\Uuid;



class backofficeController
{
    private $c;

    public function __construct(\Slim\Container $c)
    {
        $this->c = $c;
    }

    public function deleteEvent(Request $req, Response $resp, array $args): Response {
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->c->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $response = $client->delete('/events');

        $resp->getBody()->write($response->getBody());
        return writer::json_output($resp, $response->getStatusCode());
    }

    
    public function deleteUser(Request $req, Response $resp, array $args): Response{
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->c->get('settings')['auth_service'],
            'timeout' => 5.0
        ]);

        $response = $client->delete('/users');

        $resp->getBody()->write($response->getBody());
        return writer::json_output($resp, $response->getStatusCode());
    }


    public function auth(Request $req, Response $resp, array $args): Response{
        
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->c->get('settings')['auth_service'],
            'timeout' => 5.0
        ]);

        $response = $client->post('/auth');

        $resp->getBody()->write($response->getBody());
        return writer::json_output($resp, $response->getStatusCode());
    }
}
