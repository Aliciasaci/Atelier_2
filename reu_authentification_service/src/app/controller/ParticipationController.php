<?php

namespace reu\authentification\app\controller;

use DateTime;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Slim\Container;
use reu\authentification\app\models\Participant;
use reu\authentification\app\models\User;
use reu\authentification\app\utils\Writer;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class REUAuthController
 * @package lbs\command\api\controller
 */
class ParticipationController //extends Controller
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function createInvitation(Request $req, Response $resp, array $args): Response
    {

        $data = $req->getParsedBody();
        try {
            $id_par = random_bytes(32);
            $id_par = bin2hex($id_par);

            if (!isset($data['idEvent'])) {
                return Writer::json_error($resp, 401, 'Missing idEvent');
            }
            if (!isset($data['idUser'])) {
                return Writer::json_error($resp, 401, 'Missing idUser');
            }
            if (!isset($data['response'])) {
                return Writer::json_error($resp, 401, 'Missing response');
            }

            $parti = new Participant();
            $parti->id = $id_par;
            $parti->response = filter_var($data['response'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $parti->idEvent = filter_var($data['idEvent'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $parti->idUser = filter_var($data['idUser'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $parti->save();

            $data_rep = [
                "Invitation" => [
                    "id" => $id_par,
                    "idEvent" => $data['idEvent'],
                    "idUser" => $data['idUser'],
                    "response" => $data['response'],
                ]
            ];
            return Writer::json_output($resp, 200,  $data_rep);
        } catch (ModelNotFoundException $e) {
            $data_rep = [
                "type" => "error",
                "error" => "404",
                "message" => "Une erreur est survenu lors de la création de l'invitation"
            ];
            return Writer::json_output($resp, 404, $data_rep);
        }
    }

    //Get tous les users qui ont dit oui à une invitation
    public function getParByIdEvent(Request $req, Response $resp, array $args): Response  //! revoir les vérifications etc 
    {
        $id_event = $args['id'] ?? null;
        $participations = Participant::select(['id', 'idEvent', 'idUser', 'response'])->where('idEvent', '=', $id_event)->where('response', '=', 'oui')->get();

        $users = [];
        foreach ($participations as $part) {
            $user = User::select('id', 'username', 'email')->where('id', '=', $part->idUser)->get();
            array_push($users, $user);
        }
        $data_resp = [
            "participations" => $users
        ];

        return Writer::json_output($resp, 200, $data_resp);
    }

    //Get tous les users qui ont dit non à une invitation
    public function getNonParByIdEvent(Request $req, Response $resp, $args): Response  //! revoir les vérifications etc 
    {
        $id_event = $args['id'];
        $participations = Participant::select(['id', 'idEvent', 'idUser', 'response'])->where('idEvent', '=', $id_event)->where('response', '=', 'non')->get();

        $users = [];
        foreach ($participations as $part) {
            $user = User::select('id', 'username', 'email')->where('id', '=', $part->idUser)->get();
            array_push($users, $user);
        }
        $data_resp = [
            "participations" => $users
        ];

        return Writer::json_output($resp, 200, $data_resp);
    }

    //Get toutes les invitations reçus par un user
    public function getInvitationsByUserId(Request $req, Response $resp, $args): Response
    {
        $id_user = $args['id'];
        if ($id_user) {
            $invitations = Participant::select(['id', 'idEvent', 'idUser', 'response'])->where('idUser', '=', $id_user)->where('response', '=',"")->get();
            $data_resp = [
                "type" => "collection",
                "invitations" => $invitations,
                "idUser" => $id_user
            ];
        } else {
            $data_rep = [
                "type" => "error",
                "error" => "404",
                "message" => "User not found"
            ];
            return Writer::json_output($resp, 404, $data_rep);
        }
        return Writer::json_output($resp, 200, $data_resp);
    }

    /* Modifier une participation pour indiquer la réponse du user
     *  Une participation avant réponse est une invitation
     */
    public function updateInvitation(Request $req, Response $resp, array $args): Response
    {
        $response= $req->getParsedBody()['response']  ?? null;
        $id_invitation = $args['id'] ?? null;

        if (!isset($id_invitation)) {
            return Writer::json_error($resp, 400, "missing id invitations");
            $this->c->get('logger.error')->error("error : missing id invitations'");
        };
        try {

            $invit = Participant::Select()->findOrFail($id_invitation);
            $invit->response = filter_var($response, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $invit->save();

            $resp->getBody()->write(json_encode($invit));
            return Writer::json_output($resp, 200);
        } catch (ModelNotFoundException $e) {
            return Writer::json_error($resp, 404, "invitation not found : {$args}");
            $this->c->get('logger.error')->error("error : 'invitation not found : {$args}'");
        } catch (\Exception $e) {
            return Writer::json_error($resp, 500, $e->getMessage());
            $this->c->get('logger.error')->error("error :" . $e->getMessage());
        }
        return $resp;
    }
}