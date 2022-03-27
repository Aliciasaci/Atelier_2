<?php

namespace reu\backoffice\app\middleware;

use reu\backoffice\app\errors\Writer;
use \Slim\Container;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//JWT classes
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;


class Middleware
{

    private $c;

    public function __construct(Container $c)
    {
        $this->c = $c;
    }

    public function putIntoJson($rq, $rs, callable $next)
    {
        $rs = $rs->withHeader("Content-Type", "application/json;charset=utf-8");
        return $next($rq, $rs);
    }


    public static function corsHeaders(Request $req, Response $resp, callable $next): Response
    {

        $response = $next($req, $resp);

        $response = $response->withHeader('Access-Control-Allow-Origin', $req->getHeader('Origin'))
            ->withHeader('Access-Control-Allow-Methods', 'POST, PUT, GET, DELETE')
            ->withHeader('Access-Control-Allow-Headers', 'Authorization, Content-Type')
            ->withHeader('Access-Control-Max-Age', 3600)
            ->withHeader('Access-Control-Allow-Credentials', 'true');
        return $response;
    }



    public function check(Request $req, Response $resp, callable $next): Response
    {
        try {
            //le secret est conservé dans le container de dépendances
            $secret = $this->c->settings['secret'];

            //le token est récupéré et scanné depuis le header "Authorization" de la requête
            $h = $req->getHeader('Authorization')[0];
            $tokenstring = sscanf($h, "Bearer %s")[0];
            $token = JWT::decode($tokenstring, new Key($secret, 'HS512'));

            return $next($req, $resp);

        } catch (ExpiredException $e) {
            return Writer::json_error($resp, 401, 'Token expiré');
        } catch (SignatureInvalidException $e) {
            return Writer::json_error($resp, 401, 'Signature de token invalide');
        } catch (BeforeValidException $e) {
            return Writer::json_error($resp, 401, 'BeforeValidException');
        } catch (\UnexpectedValueException $e) {
            return Writer::json_error($resp, 401, 'Valeur du token incorrecte');
        }
        return $resp;
    }
}
