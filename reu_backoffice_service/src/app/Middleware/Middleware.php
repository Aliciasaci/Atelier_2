<?php
namespace reu\backoffice\app\middleware;
use reu\backoffice\app\errors\Writer;
use \Slim\Container;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Middleware{

    private $c;

    public function __construct(Container $c){
        $this->c = $c;
    }

    public function putIntoJson($rq, $rs, callable $next){
        $rs = $rs->withHeader("Content-Type", "application/json;charset=utf-8");
        return $next($rq,$rs);
    }

    public static function corsHeaders(Request $req,Response $resp,callable $next ): Response {
                
                $response = $next($req,$resp);
        
                $response = $response->withHeader('Access-Control-Allow-Origin', $req->getHeader('Origin'))
                                        ->withHeader('Access-Control-Allow-Methods', 'POST, PUT, GET, DELTE' )
                                        ->withHeader('Access-Control-Allow-Headers','Authorization, Content-Type' )
                                        ->withHeader('Access-Control-Max-Age', 3600)
                                        ->withHeader('Access-Control-Allow-Credentials', 'true');
                return $response;
        
        }

}