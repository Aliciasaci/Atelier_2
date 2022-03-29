<?php

namespace reu\authentification\app\controller;

use DateTime;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Slim\Container;
use reu\authentification\app\models\User;
use reu\authentification\app\utils\Writer;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Ramsey\Uuid\Uuid;

/**
 * Class REUAuthController
 * @package lbs\command\api\controller
 */
class REUAuthController //extends Controller
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function authenticate(Request $rq, Response $rs, array $args): Response
    {

        if (!$rq->hasHeader('Authorization')) {

            $rs = $rs->withHeader('WWW-authenticate', 'Basic realm="users_api api" ');
            return Writer::json_error($rs, 401, 'No Authorization header present');
        };


        $authstring = base64_decode(explode(" ", $rq->getHeader('Authorization')[0])[1]);
        list($email, $pass) = explode(':', $authstring);

        try {
            $user = User::select('id', 'username', 'email', 'password', 'refresh_token', 'created_at', 'description', 'updated_at', 'role')
                ->where('email', '=', $email)
                ->firstOrFail();


            if (!password_verify($pass, $user->password))
                throw new \Exception("password check failed");

            unset($user->password);
        } catch (ModelNotFoundException $e) {
            $rs = $rs->withHeader('WWW-authenticate', 'Basic realm="reu authentification" ');
            return Writer::json_error($rs, 401, 'Cet utilisateur n\'existe pas');
        } catch (\Exception $e) {
            $rs = $rs->withHeader('WWW-authenticate', 'Basic realm="reu authentification" ');
            return Writer::json_error($rs, 401, $e->getMessage());
        }

        $secret = $this->container->settings['secret'];
        $token = JWT::encode(
            [
                'iss' => 'http://api.authentification.local/auth',
                'aud' => 'http://api.backoffice.local',
                'iat' => time(),
                'exp' => time() + (12 * 30 * 24),  //validité 30 jours
                'upr' => [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'username' => $user->username,
                ]
            ],
            $secret,
            'HS512'
        );

        $user->refresh_token = bin2hex(random_bytes(32));
        $user->save();
        $data = [
            'token' =>  $token,
            'refresh_token' => $user->refresh_token,
            'user' => $user
        ];

        return Writer::json_output($rs, 200, $data);
    }

    public function deleteUser($user)
    {

        $date_now = new  \DateTime();
        $date_12month = date('Y-m-d H:i:s', strtotime("+12 months", strtotime($user['last_connected'])));

        $temp = date_diff(new \DateTime($date_12month), $date_now)->format('%R');
        if ($temp === '+') {
            $user->delete();
            return $user;
        } else {
            return false;
        }
    }

    //role 100 représente un user qui possède un compte
    //un role 200 représente un user admin
    //un role 0 représente un visiteur qui ne possède pas de compte

    public function create(Request $req, Response $resp, array $args): Response
    {
        $data = $req->getParsedBody() ?? null;
        if ($req->getAttribute('has_errors')) {
            $errors = $req->getAttribute('errors');
            $rs = $resp->withStatus(400);
            $body = json_encode([
                "type" => "error",
                "error" => "400",
                "message" => $errors
            ]);

            $rs->getBody()->write($body);
            return $rs;
        } else {
            try {
                $username = htmlspecialchars($data['uname'], ENT_QUOTES);
                $email = filter_var($data["mail"], FILTER_SANITIZE_EMAIL);
                $pwd = htmlspecialchars($data["pwd"], ENT_QUOTES);

                $id = Uuid::uuid4();

                $user = new User();
                $user->id = $id;
                $user->username = $username;
                $user->email = $email;
                $user->refresh_token = '';
                $user->description = '';
                $user->password = password_hash($pwd, PASSWORD_DEFAULT);
                $user->role = 100;
                $user->save();

                $body = json_encode([
                    "User" => [
                        "id" => $id,
                        "uname" => $username,
                        "mail" => $email,
                        "pwd" => $pwd,
                    ]
                ]);
                $rs = $resp->withStatus(201);
            } catch (ModelNotFoundException $e) {
                $rs = $rs->withStatus(404);
                $body = json_encode([
                    "type" => "error",
                    "error" => "404",
                    "message" => "Une erreur est survenu lors de la création du compte, réessayer ultérieurement !"
                ]);
            }
        }
        return Writer::json_output($rs, 200);
    }

    public function delete(Request $req, Response $resp, array $args): Response
    {
        try {
            $users = User::all();

            $json = [];

            foreach ($users as $user) {
                $response = $this->deleteUser($user);
                if ($response) {
                    array_push($json, $response);
                }
            }

            $resp = $resp->withStatus(201);
            $body = json_encode([
                "lenghth" => count($json),
                "users" => $json,
                "response" => "the users have been deleted",
            ]);
        } catch (ModelNotFoundException $e) {
            $rs = $resp->withStatus(404);
            $body = json_encode([
                "type" => "error",
                "error" => "404",
                "message" => "Une erreur est survenu lors de la suppression du compte, réessayer ultérieurement !"
            ]);
        }
        $resp->getBody()->write($body);
        return $resp;
    }

    //Rechercher un user par son psuedo ou e-mail
    public function searchUser(Request $req, Response $resp, array $args): Response
    {
        $search_input = $req->getParsedBody() ?? null;

        if (!isset($search_input['search'])) {
            ($this->c->get('logger.error'))->error("error: empty input");
            return Writer::json_error($resp, 403, "empty input, le champ à rechercher ne doit pas être vide");
        } else {
            try {
                $searched_user = User::select(['id', 'username', 'email'])->where("username", "=", $search_input)->orwhere("email", "=", $search_input)->firstOrFail();
                $datas_resp = [
                    "type" => "user",
                    "result" => $searched_user,
                ];

                return Writer::json_output($resp, 200, $datas_resp);
            } catch (ModelNotFoundException $e) {
                return Writer::json_error($resp, 404, 'Ressource not found ');
            } catch (\Exception $e) {
                return Writer::json_error($resp, 500, 'Server Error : Can\'t create event' . $e->getMessage());
            }
        }
    }

    //Créer une instance de visiteur avec seulement un ID et un username
    public function createVisiteur(Request $req, Response $resp, array $args): Response
    {
        $data = $req->getParsedBody() ?? null;
        if ($req->getAttribute('has_errors')) {
            $errors = $req->getAttribute('errors');
            $rs = $resp->withStatus(400);
            $body = json_encode([
                "type" => "error",
                "error" => "400",
                "message" => $errors
            ]);

            $rs->getBody()->write($body);
            return $rs;
        } else {
            try {
                $username = htmlspecialchars($data['username'], ENT_QUOTES);
                $id = Uuid::uuid4();

                $user = new User();
                $user->id = $id;
                $user->username = $username;
                $user->role = 0;
                $user->save();


                //Lui crée un access token et refresh token.
                $secret = $this->container->settings['secret'];
                $token = JWT::encode(
                    [
                        'iss' => 'http://api.authentification.local/auth',
                        'aud' => 'http://api.backoffice.local',
                        'iat' => time(),
                        'exp' => time() + (12 * 30 * 24),  //validité 30 jours
                        'upr' => [
                            'user_id' => $user->id,
                            'username' => $user->username,
                        ]
                    ],
                    $secret,
                    'HS512'
                );

                $user->refresh_token = bin2hex(random_bytes(32));
                $user->save();
                $response = [
                    "visiteur" => [
                        "id" => $user->id,
                        "role" => $user->role,
                        "username" => $user->username,
                        'token' =>  $token,
                        'refresh_token' => $user->refresh_token
                    ]
                ];
                return Writer::json_output($resp, 200, $response);
            } catch (ModelNotFoundException $e) {
                $response = [
                    "type" => "error",
                    "error" => "404",
                    "message" => "Une erreur est survenu lors de la création du compte, réessayer ultérieurement !"
                ];
                return Writer::json_output($resp, 400, $response);
            }
        }
    }

    //Get les informations d'un user
    public function getUserInformations(Request $req, Response $resp, array $args): Response
    {
        $user_id = $args['id'] ?? null;

        if (!isset($user_id)) {
            ($this->c->get('logger.error'))->error("error: empty input");
            return Writer::json_error($resp, 403, "empty input");
        } else {
            try {
                $user = User::select(['username', 'email', 'id', 'description','sexe','dn','tel'])->where("id", "=", $user_id)->firstOrFail();
                $datas_resp = [
                    "type" => "user",
                    "result" => $user,
                ];

                return Writer::json_output($resp, 200, $datas_resp);
            } catch (ModelNotFoundException $e) {
                return Writer::json_error($resp, 404, 'Ressource not found ');
            } catch (\Exception $e) {
                return Writer::json_error($resp, 500, 'Server Error : Can\'t create event' . $e->getMessage());
            }
        }
    }


    //Modifier les information du profil user dans
    public function updateUserInformations(Request $req, Response $resp, array $args): Response
    {
        $user_id = $args['id'] ?? null;
        $user_inforamtions = $req->getParsedBody()['params'] ?? null;

        if (!isset($user_id)) {
            return Writer::json_error($resp, 400, "missing 'id Createur'");
            $this->c->get('logger.error')->error("error : missing input 'id Createur'");
        };
        try {

            $new_user = User::Select(['id', 'username', 'email', 'description', 'dn', 'sexe'])->findOrFail($user_id);

            //Filtrer les données reçues
            if (isset($user_inforamtions[0])) {
                $new_user->username = filter_var($user_inforamtions[0], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
            if (isset($user_inforamtions[1])) {
                $new_user->email = filter_var($user_inforamtions[1], FILTER_VALIDATE_EMAIL);
            }
            if (isset($user_inforamtions[2])) {
                $new_user->description = filter_var($user_inforamtions[2], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
            if (isset($user_inforamtions[3])) {
                $new_user->sexe = filter_var($user_inforamtions[3], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
            if (isset($user_inforamtions[4])) {
                $new_user->tel = filter_var($user_inforamtions[4], FILTER_VALIDATE_INT);
            }
            if (isset($user_inforamtions[5])) {
                $new_user->dn = $user_inforamtions[5];
            }
            $new_user->save();

            $response = [
                "user" => $new_user
            ];

            return Writer::json_output($resp, 200, $response);
        } catch (ModelNotFoundException $e) {
            return Writer::json_error($resp, 404, "user inconnue : {$args}");
            $this->c->get('logger.error')->error("error : 'user inconnue : {$args}'");
        } catch (\Exception $e) {
            return Writer::json_error($resp, 500, $e->getMessage());
            $this->c->get('logger.error')->error("error :" . $e->getMessage());
        }
        return $resp;
    }
}
