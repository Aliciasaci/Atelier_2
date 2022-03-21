<?php
require_once  __DIR__ . '/../src/vendor/autoload.php';
use reu\events\app\bootstrap\ReuBootstrap;

$config = require_once __DIR__ . '/../src/app/conf/settings.php';
$dependencies = require_once __DIR__ . '/../src/app/conf/dependencies.php';
$errors = require_once __DIR__ . '/../src/app/conf/errors.php';


$c = new \Slim\Container(array_merge($config,$dependencies,$errors));
$app = new \Slim\App($c);
ReuBootstrap::startEloquent($c->settings['dbfile']);

require_once __DIR__ . '/../src/app/routes/routes.php';

$app->run();





































































// $app->get('/commandes[/]',function(Request $rq, Response $rs, array $args) : response {
    
//     $controleur = new \lbs\command\app\controller\Controller($this);
//     return  $controleur->commandes($rq,$rs,$args);
//     }
// );



// $data = ["type" => 'collection',"count" => 3, "commandes"=>[["id"=>"AuTR4-65ZTY","mail_client"=>"jan.neymar@yaboo.fr","date_commande"=>"2022-01-05 12:00:23","montant"=>25.95],
// ["id"=>"657GT-I8G443","mail_client"=>"jan.neplin@gmal.fr","date_commande"=>"2022-01-06 16:05:47","montant"=>42.95],["id"=>"K9J67-4D6F5","mail_client"=>"claude.francois@grorange.fr","date_commande"=>"2022-01-07 17:36:45","montant"=>14.95]]];


// $id_commande = $args['id'];
// $data = ["type" => 'collection',"count" => 3, "commandes"=>[["id"=>"AuTR4-65ZTY","mail_client"=>"jan.neymar@yaboo.fr","date_commande"=>"2022-01-05 12:00:23","montant"=>25.95],
// ["id"=>"657GT-I8G443","mail_client"=>"jan.neplin@gmal.fr","date_commande"=>"2022-01-06 16:05:47","montant"=>42.95],["id"=>"K9J67-4D6F5","mail_client"=>"claude.francois@grorange.fr","date_commande"=>"2022-01-07 17:36:45","montant"=>14.95]]];
// $rs=$rs->withHeader("Content-Type","application/json;charset=utf-8");



// foreach ($data["commandes"] as $key => $value) {
//     if ($data["commandes"][$key]['id'] === $id_commande){
//         $data_with_id["id"]= $data["commandes"][$key]["id"];
//         $data_with_id["mail_client"]= $data["commandes"][$key]["mail_client"];
//         $data_with_id["date_commande"]= $data["commandes"][$key]["date_commande"];
//     }
// }