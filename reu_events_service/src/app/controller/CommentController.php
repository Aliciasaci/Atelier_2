<?php

namespace reu\events\app\controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use reu\events\app\models\Comment;
use reu\events\app\errors\Writer;
use Ramsey\Uuid\Uuid;


class CommentController
{
   private $c;

   public function __construct(\Slim\Container $c)
   {
      $this->c = $c;
   }

   public function getCommentsOfEvent(Request $req, Response $resp, array $args): Response
   {
      $id_event = $args['id'];

      try {
         $comments = Comment::select(['id', 'idEvent', 'idUser', 'content'])
            ->where('idEvent', '=', $id_event)
            ->get();

         $data_response = [
            "type" => "comments",
            "data" => $comments
         ];

         return writer::json_output($resp, 200, $data_response);
      } catch (ModelNotFoundException $e) {
         $clientError = $this->c->clientError;
         return $clientError($req, $resp, 404, "Comment not found");
      }
   }

   public function createComment(Request $req, Response $resp, array $arg): Response
   {
      $comment_data = $req->getParsedBody() ?? null;

         try {
            $idEvent = htmlspecialchars($comment_data["id_event"], ENT_QUOTES);
            $idUser = htmlspecialchars($comment_data["id_user"], ENT_QUOTES);
            $content = htmlspecialchars($comment_data["content"], ENT_QUOTES);


            $id = Uuid::uuid4();

            $comment = new Comment();
            $comment->id = $id;
            $comment->idEvent = $idEvent;
            $comment->idUser = $idUser;
            $comment->content = $content;
            $comment->save();

            $data_resp = [
               "comment" => [
                  "id" => $id,
                  "event" => $idEvent,
                  "user" => $idUser,
                  "content" => $content,

               ]
            ];
       
           return Writer::json_output($resp, 200, $data_resp);
         } catch (ModelNotFoundException $e) {
            $body = [
               "type" => "error",
               "error" => "404",
               "message" => "Une erreur est survenu lors de la création du commentaire, réessayer ultérieurement !"
            ];
            return writer::json_output($resp, 404, $body);
         }
      }
}
