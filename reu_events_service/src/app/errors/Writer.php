<?php

namespace reu\events\app\errors;

use \Psr\Http\Message\ResponseInterface as Response ;
// use Psr\Container\ContainerInterface;


class Writer extends \Exception{

    public static function json_error(Response $resp, int $code_error, string $msg) : Response{

        $data = [
            'type' => 'error',
            'error' => $code_error,
            'message' => $msg
        ];

        $resp = $resp->withStatus($code_error)
                     ->withHeader('Content-Type', 'application/json; charset=utf-8');

        $resp->getBody()->write(json_encode($data));

        return $resp;

    }

    public static function json_output(Response $rs, int $status, array $data = []) : Response {
        $data_json = json_encode($data);
        
        $rs = $rs->withStatus($status);
        $rs = $rs->withHeader('Content-Type', 'application/json;charset=utf-8');

        $rs->getBody()->write($data_json);

        return $rs;
    }
}