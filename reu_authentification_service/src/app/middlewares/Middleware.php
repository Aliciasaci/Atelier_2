<?php
namespace reu\authentification\app\middlewares;
use \Respect\Validation\Validator as V;
use reu\authentification\app\utils\Writer;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Middleware{

    public function putIntoJson($rq, $rs, callable $next){
        $rs = $rs->withHeader("Content-Type", "application/json;charset=utf-8");
        return $next($rq,$rs);
    }
    
    public static function user_validators(){
        //*tableau de validateurs
        return [
            //'id' => V::stringType(),
            'Uname' => V::stringType(),
            'mail' => V::email(),
            'pwd' => V::stringType(),
            'desc' => V::stringType(),
        ];
    }
}