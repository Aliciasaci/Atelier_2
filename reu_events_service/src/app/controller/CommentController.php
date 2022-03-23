<?php
namespace reu\events\app\controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use reu\events\app\models\Comment;
use reu\events\app\errors\Writer;

class CommentController{
   private $c;

   public function __construct(\Slim\Container $c){
     $this->c = $c;
   }

   public function getCommentsFromEvent(Request $req, Response $resp, array $args): Response
   {
      $id_event = $args['idEvent']; 

      try {
         $comments = Comment::select(['id','idEvent','idUser','content','created_at','updated_at'])
            ->where('idEvent','=', $id_event)
            ->get(); 

         $data_response = [
            "type" => "comments",
            "data" => $comments
         ]; 

         $resp->getBody()->write(json_encode($data_response));
         return writer::json_output($resp, 200);
      } catch (ModelNotFoundException $e) {
         $clientError = $this->c->clientError;
         return $clientError($req, $resp, 404, "Comment not found");

      }
   }
   
   public function create(Request $req, Response $resp, array $arg) : Response 
   {
      if ($req->getAttribute('has_errors')) {
         $errors = $req->getAttribute('errors');
         $rs = $resp->withStatus(400);

         $body = json_encode([
            "type" => "error",
            "error" => "400",
            "message" => $errors
         ]);

         $rs = $rs->withHeader('Content-Type', 'application/json;charset=utf-8');
         $rs->getBody()->write($body);
         return $rs;
      } else {
         try {
            $args = $req->getParsedBody();
            $idEvent = htmlspecialchars($args["event"], ENT_QUOTES);
            $idUser = htmlspecialchars($args["user"], ENT_QUOTES);
            $content = htmlspecialchars($args["content"], ENT_QUOTES);

            $currentDateTime = date('Y-m-d H:i');

            $id = random_bytes(36);
            $id = bin2hex($id);

            $comment = new Comment();
            $comment->id = $id;
            $comment->idEvent = $idEvent;
            $comment->idUser = $idUser;
            $comment->content = $content;
            $comment->created_at = $currentDateTime;
            $comment->updated_at = $currentDateTime;
            $comment->save();

            $body = json_encode([
               "comment" => [
                  "id" => $id,
                  "event" => $idEvent,
                  "user" => $idUser,
                  "content" => $content,
                  "created" => $currentDateTime,
                  "updated" => $currentDateTime
               ]
            ]);
            $rs = $resp->withStatus(201);
         }
         catch(ModelNotFoundException $e) {
            $rs = $rs->withStatus(404);
            $body = json_encode([
               "type" => "error",
               "error" => "404",
               "message" => "Une erreur est survenu lors de la création du commentaire, réessayer ultérieurement !"
            ]);
         }
         $rs = $rs->withHeader('Content-Type', 'application/json;charset=utf-8');
         $rs->getBody()->write($body);
      } 
   return $resp;
   }
}