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

    //================= Users qui possèdent un compte =================//
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

    //Inscription
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

    
    //Supprimer les users dont l'inactivité dépasse 1 an
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


    //================= évènement =================//

    //Créer un évènement
    public function createEvent(Request $req, Response $resp, array $args): Response
    {

            $data = $req->getParsedBody();
            $client = new \GuzzleHttp\Client([
                'base_uri' => $this->c->get('settings')['events_service'],
                'timeout' => 5.0
            ]);

            $response = $client->request('POST', '/events', [
                'form_params' => [
                    'titre' => $data['titre'],
                    'description' => $data['description'],
                    'dateEvent' => $data['dateEvent'],
                    'lieu' => $data['lieu'],
                    'idCreateur' => $data['idCreateur'],
                ], 
                'headers' => [
                    'Authorization' => $req->getHeader('Authorization')
                ]
            ]);

            $resp = $resp->withStatus($response->getStatusCode())->withHeader('Content-Type', $response->getHeader('Content-Type'))
                ->withBody($response->getBody());
            return $resp;
        }
    
    //récupèrer un event par createur
    public function getEventByIdCreator(Request $req, Response $resp, array $args): Response
    {
        $id_creator = $args['id'] ?? null;
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->c->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $response = $client->request('GET', '/events/creators/' . $id_creator);
        $resp = $resp->withStatus($response->getStatusCode())->withHeader('Content-Type', $response->getHeader('Content-Type'))
            ->withBody($response->getBody());
        return $resp;
    }


    //récupèrer un seul event par son ID
    public function getOneEvent(Request $req, Response $resp, array $args): Response
    {
        $id_event = $args['id'] ?? null;
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->c->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $response = $client->request('GET', '/events/' . $id_event);

        $resp = $resp->withStatus($response->getStatusCode())->withHeader('Content-Type', $response->getHeader('Content-Type'))
            ->withBody($response->getBody());
        return $resp;
    }

    //récupèrer tous les events
    public function getEvents(Request $req, Response $resp, array $args): Response
    {
        $id_event = $args['id'] ?? null;
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->c->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $response = $client->request('GET', '/events');

        $resp = $resp->withStatus($response->getStatusCode())->withHeader('Content-Type', $response->getHeader('Content-Type'))
            ->withBody($response->getBody());
        return $resp;
    }


    //Supprimer un event par son id
    public function deleteEventById(Request $req, Response $resp, array $args): Response
    {
        $id_event = $args['id'] ?? null;
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->c->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $response = $client->request('DELETE', '/events/' . $id_event);

        $resp = $resp->withStatus($response->getStatusCode())->withHeader('Content-Type', $response->getHeader('Content-Type'))
            ->withBody($response->getBody());
        return $resp;
    }


    //supprimer tous les events expirés
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


    //================= Participations =================//

    //Créer une participation
    public function createInvitation(Request $req, Response $resp, array $args): Response
    {
        $data = $req->getParsedBody() ?? null;
        
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->c->get('settings')['auth_service'],
            'timeout' => 5.0
        ]);


        $response = $client->request('POST', '/invitations', [
            'form_params' => [
                'idUser' => $data['idUser'],
                'idEvent' => $data['idEvent'],
                'response' => $data['response'],
            ],
        ]);


        $resp = $resp->withStatus($response->getStatusCode())->withHeader('Content-Type', $response->getHeader('Content-Type'))
            ->withBody($response->getBody());
        return $resp;
    }

    //récupèrer les users qui ont dit oui à une invitations
    public function getParByIdEvent(Request $req, Response $resp, array $args): Response
    {
        $id_event = $args['id'] ?? null;

        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->c->get('settings')['auth_service'],
            'timeout' => 5.0
        ]);

        $response = $client->request('GET', '/events/participations/'.$id_event);

        $resp = $resp->withStatus($response->getStatusCode())->withHeader('Content-Type', $response->getHeader('Content-Type'))
            ->withBody($response->getBody());
        return $resp;
    }

     //récupèrer les users qui ont dit non à une invitations
     public function getNonParByIdEvent(Request $req, Response $resp, array $args): Response
     {
         $id_event = $args['id'] ?? null;
 
         $client = new \GuzzleHttp\Client([
             'base_uri' => $this->c->get('settings')['auth_service'],
             'timeout' => 5.0
         ]);
 
         $response = $client->request('GET', '/events/non_participations/'.$id_event);
 
         $resp = $resp->withStatus($response->getStatusCode())->withHeader('Content-Type', $response->getHeader('Content-Type'))
             ->withBody($response->getBody());
         return $resp;
     }

     //récupérer toutes les invitations d'un user
     public function getInvitationsByUserId(Request $req, Response $resp, array $args): Response
     {
         $id_user = $args['id'] ?? null;
 
         $client = new \GuzzleHttp\Client([
             'base_uri' => $this->c->get('settings')['auth_service'],
             'timeout' => 5.0
         ]);
 
         $response = $client->request('GET', '/users/'.$id_user.'/invitations/');
 
         $resp = $resp->withStatus($response->getStatusCode())->withHeader('Content-Type', $response->getHeader('Content-Type'))
             ->withBody($response->getBody());
         return $resp;
     }

     //Modifier une invitation pour y mettre la réponse du user invité
     public function updateInvitation(Request $req, Response $resp, array $args): Response
     {
         $id_invitation = $args['id'] ?? null;
         $response = $req->getParsedBody()['response'] ?? null;
 
         $client = new \GuzzleHttp\Client([
             'base_uri' => $this->c->get('settings')['auth_service'],
             'timeout' => 5.0
         ]);
 
         $response = $client->request('PUT', '/invitations/'. $id_invitation, [
            'form_params' => [
                'response' => $response,
            ],
         ]);
 
         $resp = $resp->withStatus($response->getStatusCode())->withHeader('Content-Type', $response->getHeader('Content-Type'))
             ->withBody($response->getBody());
         return $resp;
     }
 
     //rechercher un user par son username ou e-mail
     public function searchUser(Request $req, Response $resp, array $args): Response
     {
         $data = $req->getParsedBody() ?? null;
         
         $client = new \GuzzleHttp\Client([
             'base_uri' => $this->c->get('settings')['auth_service'],
             'timeout' => 5.0
         ]);
 
 
         $response = $client->request('POST', '/searches', [
             'form_params' => [
                 'search' => $data['search'],
             ],
         ]);
 
 
         $resp = $resp->withStatus($response->getStatusCode())->withHeader('Content-Type', $response->getHeader('Content-Type'))
             ->withBody($response->getBody());
         return $resp;
     }

     //Créer une instance de visiteur
     public function createVisiteur(Request $req, Response $resp, array $args): Response
     {
         $data = $req->getParsedBody() ?? null;
         
         $client = new \GuzzleHttp\Client([
             'base_uri' => $this->c->get('settings')['auth_service'],
             'timeout' => 5.0
         ]);
 
 
         $response = $client->request('POST', '/visiteurs', [
             'form_params' => [
                 'username' => $data['username'],
             ],
         ]);
 
 
         $resp = $resp->withStatus($response->getStatusCode())->withHeader('Content-Type', $response->getHeader('Content-Type'))
             ->withBody($response->getBody());
         return $resp;
     }

       //récupérer les informations d'un user par son id
       public function getUserInformations(Request $req, Response $resp, array $args): Response
       {
           $id_user = $args['id'] ?? null;
   
           $client = new \GuzzleHttp\Client([
               'base_uri' => $this->c->get('settings')['auth_service'],
               'timeout' => 5.0
           ]);
   
           $response = $client->request('GET', '/users/'.$id_user);
   
           $resp = $resp->withStatus($response->getStatusCode())->withHeader('Content-Type', $response->getHeader('Content-Type'))
               ->withBody($response->getBody());
           return $resp;
       }
  

}
