<?php
namespace lbs\comauthentificationmand\app\middleware;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\Container;
use reu\authentification\app\utils\Writer;

//JWT classes
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException ;
use Firebase\JWT\BeforeValidException;

// Le traitement doit procéder à la vérification du token : le système doit vérifier la présence et la valeur du
// token transmis lors de cette requête. On doit prévoir 2 modes de transport du token :
// • transport dans l'url,
// • transport dans un header applicatif.

class Token{

    private $c;

    public function __construct(Container $c){
        $this->c = $c;
    }

    public function check(Request $req, Response $resp, $args): Response {
        try {
            //le secret est conservé dans le container de dépendances
            $secret = $this->c->settings['secret'];
    
            //le token est récupéré et scanné depuis le header "Authorization" de la requête
            $h = $req->getHeader('Authorization')[0] ;
            $tokenstring = sscanf($h, "Bearer %s")[0] ;
            $token = JWT::decode($tokenstring, new Key($secret,'HS512' ) );
    
            $response = [
                'response' => 'Vérification du token réussie'
            ];
    
            return Writer::json_output($resp, 200, $response);
        } 
        catch (ExpiredException $e) {
            return Writer::json_error($resp, 401, 'Token expiré' );
    
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

