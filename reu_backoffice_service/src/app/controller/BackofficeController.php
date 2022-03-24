<?php

namespace reu\backoffice\app\controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use reu\backoffice\app\errors\Writer;


class backofficeController
{
    private $c;

    public function __construct(\Slim\Container $c)
    {
        $this->c = $c;
    }

    public function auth(Request $req, Response $resp, array $args): Response
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->c->get('settings')['auth_service'],
            'timeout' => 5.0
        ]);

        if (!$req->hasHeader('Authorization')) {

            $resp = $resp->withHeader('WWW-authenticate', 'Basic realm="users_api api" ');
            return Writer::json_error($resp, 401, 'No Authorization header present');
        } else {
            $response = $client->request('POST', '/auth', [
                'headers' => [
                    'Authorization' => $req->getHeader('Authorization')
                ]
            ]);

            $resp = $resp->withStatus($response->getStatusCode())->withHeader('Content-Type', $response->getHeader('Content-Type'))
                ->withBody($response->getBody());
            return $resp;
        }
    }

    public function signIn(Request $req, Response $resp, array $args): Response
    {
        $data = $req->getParsedBody();
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->c->get('settings')['auth_service'],
            'timeout' => 5.0
        ]);

        $response = $client->request('POST', '/create', [
            'form_params' => [
                'mail' => $data['mail'],
                'uname' => $data['uname'],
                'pwd' => $data['pwd'],
            ],
        ]);

        $resp = $resp->withStatus($response->getStatusCode())->withHeader('Content-Type', $response->getHeader('Content-Type'))
            ->withBody($response->getBody());
        return $resp;
    }

    public function getEvent(Request $req, Response $resp, array $args): Response
    {
        $id_creator = $req->getQueryParams()['id'] ?? null;
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->c->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $response = $client->request('POST', '/events/creators/'.$id_creator);

        $resp = $resp->withStatus($response->getStatusCode())->withHeader('Content-Type', $response->getHeader('Content-Type'))
            ->withBody($response->getBody());
        return $resp;
    }

    public function deleteEvent(Request $req, Response $resp, array $args): Response
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->c->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $response = $client->delete('/events');

        $resp->getBody()->write($response->getBody());
        return writer::json_output($resp, $response->getStatusCode());
    }


    public function deleteUser(Request $req, Response $resp, array $args): Response
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->c->get('settings')['auth_service'],
            'timeout' => 5.0
        ]);

        $response = $client->delete('/users');

        $resp->getBody()->write($response->getBody());
        return writer::json_output($resp, $response->getStatusCode());
    }
}
